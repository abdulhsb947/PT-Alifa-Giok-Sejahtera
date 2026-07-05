<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['order.user'])->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    // ======================
    // APPROVE PAYMENT
    // ======================
    public function approve($id)
{
    DB::beginTransaction();

    try {

    $payment = Payment::whereKey($id)
        ->lockForUpdate()
        ->firstOrFail();

    if ($payment->status == 'disetujui') {
        DB::commit();

        return back()->with('error', 'Pembayaran sudah disetujui');
    }

    $payment->update(['status' => 'disetujui']);

    $order = Order::whereKey($payment->order_id)
        ->lockForUpdate()
        ->firstOrFail();

    $order->load([
        'payments',
        'rental',
        'items.product',
        'rental.returnItems'
    ]);

    $tagihan = Payment::where('order_id', $order->id)
        ->where('payment_type', 'tagihan')
        ->lockForUpdate()
        ->first();

    $totalTagihan = $tagihan ? (int) $tagihan->total_tagihan : 0;

    $totalDibayar = Payment::where('order_id', $order->id)
        ->whereIn('payment_type', ['dp', 'lunas', 'pelunasan'])
        ->where('status', 'disetujui')
        ->sum('amount');

    $sisaPembayaran = max(0, $totalTagihan - (int) $totalDibayar);

    if ($tagihan) {
        $tagihan->update([
            'amount' => (int) $totalDibayar,
            'sisa_pembayaran' => $sisaPembayaran,
            'status' => $sisaPembayaran > 0 ? 'menunggu_pembayaran' : 'disetujui',
        ]);
    }

    if ($payment->payment_type == 'dp' && $sisaPembayaran > 0) {
        $dueDate = $this->pelunasanDueDate($order);

        Payment::updateOrCreate(
            [
                'order_id' => $order->id,
                'payment_type' => 'pelunasan',
                'status' => 'menunggu_pembayaran',
            ],
            [
                'total_tagihan' => $totalTagihan,
                'amount' => 0,
                'sisa_pembayaran' => $sisaPembayaran,
                'proof' => null,
                'notes' => 'Segera lakukan pelunasan sebelum tanggal jatuh tempo ' .
    Carbon::parse($dueDate)->format('d M Y'),
                'due_date' => $dueDate,
            ]
        );

        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Tenggat Pelunasan',
            'message' => 'Pelunasan wajib dilakukan paling lambat ' . Carbon::parse($dueDate)->format('d M Y') . '.',
            'type' => 'payment',
            'is_read' => 0,
            'url' => route('customer.payment.list'),
        ]);
    }

    if ($sisaPembayaran <= 0) {
        Payment::where('order_id', $order->id)
            ->where('payment_type', 'pelunasan')
            ->where('status', 'menunggu_pembayaran')
            ->update([
                'status' => 'disetujui',
                'sisa_pembayaran' => 0,
            ]);
    }

    $order->load(['payments', 'rental', 'items.product']);

    // ======================
    // CEK PAYMENT
    // ======================
    $hasDP = $order->payments
        ->where('payment_type', 'dp')
        ->where('status', 'disetujui')
        ->count() > 0;

    $hasLunas = $order->payments
        ->where('payment_type', 'lunas')
        ->where('status', 'disetujui')
        ->count() > 0;

    $hasPelunasan = $order->payments
        ->where('payment_type', 'pelunasan')
        ->where('status', 'disetujui')
        ->count() > 0;

    $hasPendingPenalty = $order->payments
        ->where('payment_type', 'penalty')
        ->where('status', '!=', 'disetujui')
        ->count() > 0;

    // ======================
    // LOGIKA STATUS
    // ======================

    // BELUM DIKIRIM
    if (!$order->rental) {

        $order->update([
            'status' => $sisaPembayaran > 0
                ? 'menunggu_pelunasan'
                : 'telah_dibayar'
        ]);

    } else {

        // ADA DENDA
        if ($hasPendingPenalty) {

            $order->update(['status' => 'menunggu_pembayaran_denda']);

        }
        // DP TANPA PELUNASAN
        elseif ($sisaPembayaran > 0 || ($hasDP && !$hasPelunasan)) {

            $order->update(['status' => 'menunggu_pelunasan']);

        }
        // LUNAS ATAU SUDAH PELUNASAN
        elseif ($hasLunas || ($hasDP && $hasPelunasan)) {

    $shouldFinishRental = $order->rental
        && in_array($order->rental->status, [
            'menunggu_pelunasan',
            'menunggu_pembayaran_denda',
            'menunggu_pelunasan_dan_denda',
        ]);

    $order->update([
        'status' => $shouldFinishRental
            ? 'selesai'
            : ($order->rental && $order->rental->status != 'selesai'
            ? 'sewa_telah_berlaku'
            : 'selesai')
    ]);

    // ======================
    // FINALISASI STOK PENGEMBALIAN
    // ======================
    if ($shouldFinishRental) {

        foreach ($order->items as $item) {

            $product = Product::whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if ($product) {
                $returnItem = $order->rental->returnItems
                    ->firstWhere('product_id', $item->product_id);

                $damagedQty = (int) ($returnItem->damaged_qty ?? 0);
                $lostQty = (int) ($returnItem->lost_qty ?? 0);
                $normalQty = max(0, (int) $item->quantity - $damagedQty - $lostQty);

                $stockToRelease = min((int) $item->quantity, (int) $product->rented_stock);
                $stockToAvailable = min($normalQty, $stockToRelease);

                if ($stockToAvailable > 0) {
                    $product->increment('available_stock', $stockToAvailable);
                }

                $remainingRelease = $stockToRelease - $stockToAvailable;
                $stockToMaintenance = min($damagedQty, $remainingRelease);

                if ($stockToMaintenance > 0) {
                    $product->increment('maintenance_stock', $stockToMaintenance);

                    Maintenance::create([
                        'product_id' => $product->id,
                        'qty' => $stockToMaintenance,
                        'price' => $returnItem->repair_cost ?? 0,
                        'notes' => $returnItem->notes
                            ?? 'Otomatis dari pengembalian rental #' . $order->rental->id,
                        'status' => 'proses',
                    ]);
                }

                $remainingRelease -= $stockToMaintenance;
                $stockToLost = min($lostQty, $remainingRelease);

                if ($stockToLost > 0) {
                    $product->decrement('total_stock', min($stockToLost, (int) $product->total_stock));
                }

                $releasedStock = $stockToAvailable + $stockToMaintenance + $stockToLost;

                if ($releasedStock > 0) {
                    $product->decrement('rented_stock', $releasedStock);
                }
            }
        }

        // update rental
        $order->rental->update(['status' => 'selesai']);
    }
}
    }


    // ======================
    // NOTIFIKASI
    // ======================
    Notification::create([
        'user_id' => $order->user_id,
        'title' => 'Pembayaran Diterima',
        'message' => 'Pembayaran ' . $payment->payment_type . ' telah diverifikasi',
        'type' => 'payment',
        'is_read' => 0,
    'url' => route('customer.payment.list')
    ]);

    DB::commit();

    return back()->with('success', 'Pembayaran disetujui');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with('error', $e->getMessage());
    }
}

    // ======================
    // REJECT PAYMENT
    // ======================
    public function reject($id)
    {
        $payment = Payment::with('order')->findOrFail($id);

        $payment->update(['status' => 'ditolak']);

        Notification::create([
            'user_id' => $payment->order->user_id,
            'title' => 'Pembayaran Ditolak',
            'message' => 'Pembayaran ditolak, silakan upload ulang',
            'type' => 'payment'
        ]);

        return back()->with('error', 'Pembayaran ditolak');
    }

    // ======================
    // FORM CREATE TAGIHAN
    // ======================
public function create(Request $request)
{
    $order = Order::with([
        'user',
        'agreement'
    ])->findOrFail($request->order_id);

    $existingTagihan = Payment::where('order_id', $order->id)
        ->where('payment_type', 'tagihan')
        ->exists();

    if ($existingTagihan) {

        return redirect()
            ->back()
            ->with(
                'error',
                'Tagihan utama sudah pernah dibuat.'
            );
    }

    return view(
        'admin.payments.create',
        compact('order')
    );
}

    // ======================
    // STORE TAGIHAN (ADMIN)
    // ======================
    public function store(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',

        'biaya_sewa' => 'required|numeric|min:0',
        'biaya_pemasangan' => 'required|numeric|min:0',
        'biaya_pembongkaran' => 'required|numeric|min:0',
        'biaya_pengiriman' => 'required|numeric|min:0',
        'biaya_lainnya' => 'nullable|numeric|min:0',
    ]);

    $order = Order::findOrFail(
        $request->order_id
    );

    $existingTagihan = Payment::where(
        'order_id',
        $order->id
    )
    ->where(
        'payment_type',
        'tagihan'
    )
    ->exists();

    if ($existingTagihan) {

        return back()->with(
            'error',
            'Tagihan utama sudah dibuat untuk order ini.'
        );
    }

    $totalTagihan =
        $request->biaya_sewa +
        $request->biaya_pemasangan +
        $request->biaya_pembongkaran +
        $request->biaya_pengiriman +
        ($request->biaya_lainnya ?? 0);

    Payment::create([

        'order_id' => $order->id,

        'biaya_sewa' => $request->biaya_sewa,

        'biaya_pemasangan' => $request->biaya_pemasangan,

        'biaya_pembongkaran' => $request->biaya_pembongkaran,

        'biaya_pengiriman' => $request->biaya_pengiriman,

        'biaya_lainnya' => $request->biaya_lainnya ?? 0,

        'total_tagihan' => $totalTagihan,

        'sisa_pembayaran' => $totalTagihan,

        'amount' => 0,

        'payment_type' => 'tagihan',

        'status' => 'menunggu_pembayaran',

        'notes' => $request->notes,

        'proof' => null,
    ]);

    Notification::create([

        'user_id' => $order->user_id,

        'title' => 'Tagihan Baru',

        'message' =>
            'Tagihan pembayaran telah dibuat dengan total Rp ' .
            number_format($totalTagihan, 0, ',', '.'),

        'type' => 'payment',

        'is_read' => 0,

        'url' => route('customer.payment.list')
    ]);

    return redirect(
        '/admin/orders/' . $order->id
    )->with(
        'success',
        'Tagihan berhasil dibuat'
    );
}

    private function pelunasanDueDate(Order $order)
    {
        $start = Carbon::parse($order->start_date);
        $duration = max(1, (int) $order->duration);

        if ($order->duration_unit == 'hari') {
            $endDate = $start->copy()->addDays($duration);
            $dueDate = $endDate->copy()->subDays(7);

            return $dueDate->lt($start) ? $start->toDateString() : $dueDate->toDateString();
        }

        if ($order->duration_unit == 'minggu') {
            $endDate = $start->copy()->addWeeks($duration);
            $dueDate = $endDate->copy()->subDays(7);

            return $dueDate->lt($start) ? $start->toDateString() : $dueDate->toDateString();
        }

        $endDate = $start->copy()->addMonths($duration);
        $dueDate = $endDate->copy()->subDays(7);

        return $dueDate->lt($start) ? $start->toDateString() : $dueDate->toDateString();
    }
}
