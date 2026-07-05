<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\AdminNotification;


class PaymentController extends Controller
{
private array $paymentTypes = ['dp', 'lunas', 'pelunasan'];

public function paymentList()
{
    $payments = Payment::with(['order.agreement'])
    ->whereHas('order', function ($q) {
        $q->where('user_id', auth()->id());
    })
    ->latest()
    ->get();

    $pending = $payments->where('status', 'menunggu_verifikasi')->count();
    $approved = $payments->where('status', 'disetujui')->count();
    $rejected = $payments->where('status', 'ditolak')->count();

    return view('customer.payments', compact(
        'payments',
        'pending',
        'approved',
        'rejected'
    ));
}


public function uploadPayment(Request $request, $id)
{
    $validated = $request->validate([
        'amount' => 'required|integer|min:1',
        'payment_type' => 'required|in:dp,lunas,pelunasan',
        'proof' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        'notes' => 'nullable|string|max:1000',
        'edit_payment_id' => 'nullable|integer|exists:payments,id',
    ]);

    $order = Order::where('user_id', auth()->id())
        ->findOrFail($id);

    $tagihan = Payment::where('order_id', $id)
        ->where('payment_type', 'tagihan')
        ->firstOrFail();

    $editPayment = null;

    if ($request->filled('edit_payment_id')) {
        $editPayment = Payment::where('order_id', $order->id)
            ->where('id', $request->edit_payment_id)
            ->where('status', 'ditolak')
            ->whereIn('payment_type', $this->paymentTypes)
            ->firstOrFail();
    }

    $sudahDibayar = Payment::where('order_id', $id)
        ->whereIn('payment_type', $this->paymentTypes)
        ->where('status', 'disetujui')
        ->sum('amount');

    $sisaSaatIni = max(
        0,
        (int) $tagihan->total_tagihan - (int) $sudahDibayar
    );

    $amount = (int) $validated['amount'];

    if ($amount > $sisaSaatIni) {
        return back()->with(
            'error',
            'Jumlah pembayaran melebihi sisa tagihan'
        );
    }

    if (in_array($validated['payment_type'], ['lunas', 'pelunasan']) && $amount < $sisaSaatIni) {
        return back()->with(
            'error',
            'Pembayaran lunas atau pelunasan harus sesuai sisa tagihan'
        );
    }

    $path = $request->file('proof')
        ->store('payments', 'public');

    $data = [
        'order_id' => $id,
        'total_tagihan' => $tagihan->total_tagihan,
        'amount' => $amount,
        'sisa_pembayaran' => max(0, $sisaSaatIni - $amount),
        'payment_type' => $validated['payment_type'],
        'proof' => $path,
        'notes' => $validated['notes'] ?? null,
        'status' => 'menunggu_verifikasi'
    ];

    if ($editPayment) {

    $editPayment->update($data);

} else {

    $waitingPayment = Payment::where('order_id', $id)
        ->where('payment_type', $validated['payment_type'])
        ->where('status', 'menunggu_pembayaran')
        ->first();

    if ($waitingPayment) {

        $waitingPayment->update([
            'amount' => $amount,
            'proof' => $path,
            'notes' => $validated['notes'] ?? null,
            'status' => 'menunggu_verifikasi',
            'sisa_pembayaran' => max(0, $sisaSaatIni - $amount),
        ]);

    } else {

        Payment::create($data);

    }
}

    AdminNotification::create([
        'title' => $editPayment ? 'Pembayaran Diperbarui' : 'Pembayaran Baru',
        'message' => $order->order_code . ' mengirim pembayaran',
        'url' => '/admin/payments',
        'is_read' => 0
    ]);

    


    return redirect()
        ->route('customer.payment.list')
        ->with(
            'success',
            'Pembayaran berhasil dikirim'
        );
}

public function paymentPage($id)
{
    $order = Order::with(['agreement', 'user'])
        ->findOrFail($id);

    $payments = Payment::with('order.user')
        ->where('order_id', $id)
        ->latest()
        ->get();

    // Tagihan utama dari admin
    $tagihan = $payments->where('payment_type', 'tagihan')->first();

    // Hitung total pembayaran yang sudah disetujui
    $totalDibayar = $payments
        ->whereIn('payment_type', [
            'dp',
            'lunas',
            'pelunasan'
        ])
        ->where('status', 'disetujui')
        ->sum('amount');

    $editPayment = $payments
        ->where('status', 'ditolak')
        ->whereIn('payment_type', $this->paymentTypes)
        ->first();

    $hasPendingPayment = $payments
        ->where('status', 'menunggu_verifikasi')
        ->whereIn('payment_type', $this->paymentTypes)
        ->count() > 0;

    $pelunasanTagihan = $payments
        ->where('payment_type', 'pelunasan')
        ->where('status', 'menunggu_pembayaran')
        ->first();

    // Hitung sisa pembayaran
    $sisaPembayaran = $tagihan
        ? max(
            0,
            $tagihan->total_tagihan - $totalDibayar
        )
        : 0;

    $pending = $payments
        ->where('status', 'menunggu_verifikasi')
        ->count();

    $approved = $payments
        ->where('status', 'disetujui')
        ->count();

    $rejected = $payments
        ->where('status', 'ditolak')
        ->count();

        

    return view('customer.payment', compact(
        'order',
        'payments',
        'tagihan',
        'totalDibayar',
        'sisaPembayaran',
        'pending',
        'approved',
        'rejected',
        'editPayment',
        'hasPendingPayment',
        'pelunasanTagihan'
    ));
}

public function uploadProof(Request $request, $id)
{
    $request->validate([
        'proof' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $payment = Payment::findOrFail($id);

    $path = $request->file('proof')->store('payments', 'public');

    $payment->update([
        'proof' => $path,
        'status' => 'menunggu_verifikasi'
    ]);

    AdminNotification::create([
    'title' => 'Bukti Pembayaran Baru',
    'message' => $payment->order->order_code . ' mengunggah ulang bukti pembayaran',
    'url' => '/admin/payments',
    'is_read' => 0
]);

    return back()->with('success', 'Bukti pembayaran berhasil dikirim');
}

}
