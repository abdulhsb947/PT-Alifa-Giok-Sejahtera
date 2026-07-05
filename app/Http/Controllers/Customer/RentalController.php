<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Penalty;
use Carbon\Carbon;
use App\Models\Notification;


class RentalController extends Controller
{
    public function index()
    {
        $dataSewa = Rental::with([
    'order.user',
    'order.items.product',
    'order.payments',
    'penalty',
    'returnItems.product'
])
            ->whereHas('order', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        $sedangDisewa = 0;
        $selesai = 0;
        $terlambat = 0;

        foreach ($dataSewa as $sewa) {

            if (!$sewa->order) continue;

            $order = $sewa->order;

            // ======================
            // HITUNG TANGGAL
            // ======================
            $tanggalSelesai = Carbon::parse($order->start_date)
                ->addMonths($order->duration);

            // ======================
            // STATUS SEWA
            // ======================
            if ($sewa->status == 'selesai') {
                $selesai++;
            } elseif (now()->gt($tanggalSelesai)) {
                $terlambat++;
            } else {
                $sedangDisewa++;
            }

            // ======================
            // 🔥 TAMBAHAN LOGIKA PAYMENT
            // ======================

            $lastPayment = $order->payments
                ->where('status', 'disetujui')
                ->sortByDesc('created_at')
                ->first();

            $sewa->isLunas = $lastPayment && $lastPayment->payment_type == 'full';

            // ======================
            // 🔥 CEK DENDA
            // ======================
            $sewa->hasPenalty = $sewa->penalty != null;
            $sewa->penaltyPaid = $sewa->penalty && $sewa->penalty->status == 'dibayar';
        }

        return view('customer.rentals', compact(
            'dataSewa',
            'sedangDisewa',
            'selesai',
            'terlambat'
        ));
    }

    public function markReturned($id)
{
    $rental = Rental::with('order') // 🔥 sekalian eager load
        ->whereHas('order', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->findOrFail($id);

    // hanya jika masih disewakan
    if ($rental->status != 'disewakan') {
        return back()->with('error', 'Tidak bisa diproses');
    }

    $rental->update([
        'status' => 'menunggu_konfirmasi_pengembalian'
    ]);

    // 🔥 AMBIL ORDER
    $order = $rental->order;

    // 🔥 NOTIFIKASI (opsional ke admin/customer)
    if ($order) {
        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Pengembalian Diproses',
            'message' => 'Barang telah dikembalikan. Silakan cek status pembayaran.',
            'type' => 'rental'
        ]);
    }

    return back()->with('success', 'Menunggu konfirmasi admin');
}
}