<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Penalty;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;
use App\Models\ReturnItem;

class RentalController extends Controller
{
    public function index()
{
    
    $orders = Order::with('user')
        ->whereIn('status', [
            'telah_dibayar',
            'menunggu_pelunasan',
            
        ])
        ->whereDoesntHave('rental')
        ->get();

    $rentals = Rental::with([
        'order.user',
        'order.items.product',
        'penalty'
    ])->latest()->get();

    // ======================
    // HITUNG STATUS DASHBOARD
    // ======================

    $sedangDisewa = $rentals
    ->whereNotIn('status', ['selesai'])
    ->count();

$selesai = $rentals
    ->where('status', 'selesai')
    ->count();

$terlambat = $rentals->filter(function ($rental) {
    $endDate = \Carbon\Carbon::parse($rental->order->start_date)
        ->addMonths($rental->order->duration);

    return now()->gt($endDate) && $rental->status != 'selesai';
})->count();

    return view('admin.rentals', compact(
        'orders',
        'rentals',
        'sedangDisewa',
        'selesai',
        'terlambat'
    ));
}

    public function send($id)
    {
        $order = Order::findOrFail($id);

        if ($order->rental) {
            return back()->with('error', 'Order sudah disewakan');
        }

        Rental::create([
            'order_id' => $order->id,
            'tanggal_kirim' => now(),
            'status' => 'disewakan'
        ]);

        $order->update([
            'status' => 'sewa_telah_berlaku'
        ]);
        Notification::create([
        'user_id' => $order->user_id,

        'title' => 'Barang Sedang Dikirim',

        'message' =>
            'Pesanan ' .
            $order->order_code .
            ' sedang dalam proses pengiriman ke lokasi proyek.',

        'type' => 'rental',

        'is_read' => 0,

        'url' => route('customer.rentals')
    ]);

        return back()->with('success', 'Barang berhasil dikirim');
    }

    public function returnForm($id)
    {
        $rental = Rental::with('order.items.product')->findOrFail($id);
        $order = $rental->order;

        $endDate = Carbon::parse($order->start_date)
            ->addMonths($order->duration);

        $lateDays = now()->gt($endDate)
    ? $endDate->diffInDays(now())
    : 0;

// Ambil total tagihan utama
$tagihan = $order->payments
    ->where('payment_type', 'tagihan')
    ->first();

$totalTagihan = $tagihan->total_tagihan ?? 0;

// Denda 2% per hari dari total tagihan
$lateFee = $lateDays > 0
    ? ($totalTagihan * 0.02) * $lateDays
    : 0;

        return view('admin.return', compact(
            'rental',
            'lateDays',
            'lateFee'
        ));
    }

    public function return(Request $request, $id)
{
    $request->validate([
        'damage_fee' => 'nullable|numeric|min:0',
        'pelunasan_amount' => 'nullable|numeric|min:0'
    ]);

    DB::beginTransaction();

    try {

        $rental = Rental::with([
            'order.items.product',
            'order.payments',
            'order.user'
        ])
            ->whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();

        $order = $rental->order;

        $notesDp = $request->notes_dp;
        $notesPenalty = $request->notes_penalty;

        // ======================
        // HITUNG KETERLAMBATAN
        // ======================

        $endDate = Carbon::parse($order->start_date)
            ->addMonths($order->duration);

        $lateDays = now()->gt($endDate)
    ? $endDate->diffInDays(now())
    : 0;

// Ambil total tagihan utama
$tagihan = $order->payments
    ->where('payment_type', 'tagihan')
    ->first();

$totalTagihan = $tagihan->total_tagihan ?? 0;

// Denda 2% per hari dari total tagihan
$lateFee = $lateDays > 0
    ? ($totalTagihan * 0.02) * $lateDays
    : 0;

        // ======================
        // HITUNG DENDA KERUSAKAN
        // ======================

        $damageFee = $request->damage_fee ?? 0;

        $returnPenalty = 0;
        

        foreach ($order->items as $item) {

            $repairCost =
                $request->repair_cost[$item->product_id] ?? 0;

            $lostCost =
                $request->lost_cost[$item->product_id] ?? 0;

            $returnPenalty +=
                $repairCost +
                $lostCost;
        }

        $totalPenalty =
            $lateFee +
            $damageFee +
            $returnPenalty;

        // ======================
        // CEK PELUNASAN
        // ======================

        $hasDP = $order->payments
            ->where('payment_type', 'dp')
            ->where('status', 'disetujui')
            ->count() > 0;

        $hasPelunasan = $order->payments
            ->where('payment_type', 'pelunasan')
            ->where('status', 'disetujui')
            ->count() > 0;

        $hasLunas = $order->payments
            ->where('payment_type', 'lunas')
            ->where('status', 'disetujui')
            ->count() > 0;

        $needsPelunasan =
            $hasDP &&
            !$hasPelunasan &&
            !$hasLunas;

        $needsPenalty = $totalPenalty > 0;

        // ======================
        // SIMPAN DETAIL RETURN ITEM
        // ======================

        foreach ($order->items as $item) {
            $damagedQty =
                (int) ($request->damaged_qty[$item->product_id] ?? 0);

            $lostQty =
                (int) ($request->lost_qty[$item->product_id] ?? 0);

            if ($damagedQty < 0 || $lostQty < 0) {
                throw new \Exception(
                    'Jumlah barang rusak dan hilang tidak boleh minus'
                );
            }

            if (($damagedQty + $lostQty) > $item->quantity) {
                throw new \Exception(
                    'Jumlah barang rusak dan hilang melebihi jumlah barang disewa'
                );
            }

            ReturnItem::updateOrCreate(
                [
                    'rental_id' => $rental->id,
                    'product_id' => $item->product_id
                ],
                [
                    'damaged_qty' =>
                        $damagedQty,

                    'lost_qty' =>
                        $lostQty,

                    'repair_cost' =>
                        $request->repair_cost[$item->product_id] ?? 0,

                    'lost_cost' =>
                        $request->lost_cost[$item->product_id] ?? 0,

                    'notes' =>
                        $request->item_notes[$item->product_id] ?? null
                ]
            );
        }

        // ======================
        // SIMPAN PENALTY
        // ======================

        if ($needsPenalty) {

            Penalty::updateOrCreate(
                [
                    'rental_id' => $rental->id
                ],
                [
                    'late_days' => $lateDays,
                    'late_fee' => $lateFee,
                    'damage_fee' => $damageFee,
                    'total_fee' => $totalPenalty,
                    'notes' => $notesPenalty
                ]
            );

            Payment::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'payment_type' => 'penalty'
                ],
                [
                    'user_id' => $order->user_id,
                    'amount' => $totalPenalty,
                    'status' => 'menunggu_pembayaran',
                    'notes' => $notesPenalty
                ]
            );
        }

        // ======================
        // PELUNASAN
        // ======================

        if ($request->pelunasan_amount) {

    Payment::updateOrCreate(
        [
            'order_id' => $order->id,
            'payment_type' => 'pelunasan'
        ],
        [
            'user_id' => $order->user_id,
            'amount' => $request->pelunasan_amount,
            'status' => 'menunggu_pembayaran',
            'notes' => $notesDp
        ]
    );
}

        // ======================
        // STATUS ORDER
        // ======================

        if ($needsPelunasan && $needsPenalty) {

            $orderStatus =
                'menunggu_pelunasan_dan_denda';

        } elseif ($needsPelunasan) {

            $orderStatus =
                'menunggu_pelunasan';

        } elseif ($needsPenalty) {

            $orderStatus =
                'menunggu_pembayaran_denda';

        } else {

            $orderStatus =
                'selesai';
        }

        // ======================
        // UPDATE STOK SAAT SELESAI
        // ======================

        if ($orderStatus == 'selesai' && $rental->status != 'selesai') {

            foreach ($order->items as $item) {

                $product = Product::whereKey($item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    continue;
                }

                $damagedQty =
                    (int) ($request->damaged_qty[$item->product_id] ?? 0);

                $lostQty =
                    (int) ($request->lost_qty[$item->product_id] ?? 0);

                if (($damagedQty + $lostQty) > $item->quantity) {
                    throw new \Exception(
                        'Jumlah barang rusak dan hilang melebihi jumlah barang disewa'
                    );
                }

                $normalQty =
                    max(0, $item->quantity - $damagedQty - $lostQty);

                $stockToRelease =
                    min((int) $item->quantity, (int) $product->rented_stock);

                $stockToAvailable =
                    min($normalQty, $stockToRelease);

                if ($stockToAvailable > 0) {
                    $product->increment('available_stock', $stockToAvailable);
                }

                $remainingRelease =
                    $stockToRelease - $stockToAvailable;

                $stockToMaintenance =
                    min($damagedQty, $remainingRelease);

                if ($stockToMaintenance > 0) {
                    $product->increment('maintenance_stock', $stockToMaintenance);

                    Maintenance::create([
                        'product_id' => $product->id,
                        'qty' => $stockToMaintenance,
                        'price' => $request->repair_cost[$item->product_id] ?? 0,
                        'notes' => $request->item_notes[$item->product_id]
                            ?? 'Otomatis dari pengembalian rental #' . $rental->id,
                        'status' => 'proses'
                    ]);
                }

                $remainingRelease -= $stockToMaintenance;

                $stockToLost =
                    min($lostQty, $remainingRelease);

                if ($stockToLost > 0) {
                    $product->decrement('total_stock', min($stockToLost, (int) $product->total_stock));
                }

                $releasedStock =
                    $stockToAvailable +
                    $stockToMaintenance +
                    $stockToLost;

                if ($releasedStock > 0) {
                    $product->decrement('rented_stock', $releasedStock);
                }
            }
        }

        $rental->update([
            'status' => $orderStatus
        ]);

        $order->update([
            'status' => $orderStatus
        ]);

        // ======================
        // NOTIFIKASI
        // ======================

        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Pengembalian Diproses',
            'message' => 'Silakan cek status pembayaran',
            'type' => 'rental',
            'is_read' => 0,
            'url' => route('customer.rentals')
        ]);

        DB::commit();

        return redirect()
            ->route('admin.rentals')
            ->with(
                'success',
                'Pengembalian diproses'
            );

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}
}
