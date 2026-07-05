<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\AdminNotification;

class PenaltyController extends Controller
{
    // ======================
    // FORM BAYAR DENDA
    // ======================
    public function payForm($id)
{
    $penalty = Penalty::with([
        'rental.order',
        'rental.returnItems.product'
    ])
    ->whereHas('rental.order', function ($q) {
        $q->where('user_id', auth()->id());
    })
    ->findOrFail($id);

    $isEdit = Payment::where('order_id', $penalty->rental->order->id)
        ->where('payment_type', 'penalty')
        ->where('status', 'ditolak')
        ->exists();

    return view(
        'customer.penalty_payment',
        compact('penalty', 'isEdit')
    );
}

    // ======================
    // PROSES BAYAR DENDA
    // ======================
    public function pay(Request $request, $id)
{
    $request->validate([
        'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $penalty = Penalty::with('rental.order')
        ->whereHas('rental.order', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->findOrFail($id);

    $order = $penalty->rental->order;

    $file = $request->file('bukti')->store('payments', 'public');

    // Cari payment penalty yang sudah ada
    $payment = Payment::where('order_id', $order->id)
        ->where('payment_type', 'penalty')
        ->first();

    if ($payment) {

        // Update payment lama
        $payment->update([
            'amount' => $penalty->total_fee,
            'proof' => $file,
            'status' => 'menunggu_verifikasi'
        ]);

    } else {

        // Buat payment baru jika belum ada
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $penalty->total_fee,
            'payment_type' => 'penalty',
            'status' => 'menunggu_verifikasi',
            'proof' => $file
        ]);
    }

    AdminNotification::create([
        'title' => 'Pembayaran Denda',
        'message' => $order->order_code . ' mengirim bukti pembayaran denda',
        'url' => '/admin/payments',
        'is_read' => 0
    ]);

    return redirect()
        ->route('customer.payment.list')
        ->with(
            'success',
            'Bukti pembayaran denda berhasil dikirim dan sedang menunggu verifikasi admin.'
        );
}

    public function penaltyDetail($id)
{
    $penalty = Penalty::with([
        'rental.order',
        'rental.returnItems.product'
    ])
    ->findOrFail($id);

    return view(
        'customer.penalty_payment',
        compact('penalty')
    );
}
}