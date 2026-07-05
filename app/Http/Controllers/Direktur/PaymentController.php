<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{

public function payments()
{
    $payments = Payment::with([
        'order.user'
    ])
    ->latest()
    ->get();

    return view(
        'direktur.payments',
        [

            'payments' => $payments,

            'totalPayment' => $payments->count(),

            'approved' => $payments
                ->where('status','disetujui')
                ->count(),

            'pending' => $payments
                ->where('status','menunggu_verifikasi')
                ->count(),

            'rejected' => $payments
                ->where('status','ditolak')
                ->count(),

            'totalIncome' => $payments
                ->where('status','disetujui')
                ->sum('amount')

        ]
    );
}
}