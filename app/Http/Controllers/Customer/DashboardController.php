<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Product;
use App\Models\Maintenance;

class DashboardController extends Controller
{
    public function index()
{
    $userId = auth()->id();

    $totalOrders = Order::where(
        'user_id',
        $userId
    )->count();

    $activeRentals = Rental::whereHas(
        'order',
        fn($q) => $q->where('user_id', $userId)
    )
    ->whereIn('status', [
        'disewakan',
        'menunggu_konfirmasi_pengembalian'
    ])
    ->count();

    $totalPayments = Payment::whereHas(
        'order',
        fn($q) => $q->where('user_id', $userId)
    )
    ->where('status', 'disetujui')
    ->sum('amount');

    $latestOrder = Order::where(
        'user_id',
        $userId
    )
    ->latest()
    ->first();

    $latestRental = Rental::with('order')
        ->whereHas(
            'order',
            fn($q) => $q->where('user_id', $userId)
        )
        ->latest()
        ->first();

    $latestPayment = Payment::with('order')
        ->whereHas(
            'order',
            fn($q) => $q->where('user_id', $userId)
        )
        ->latest()
        ->first();

    return view(
        'customer.dashboard',
        compact(
            'totalOrders',
            'activeRentals',
            'totalPayments',
            'latestOrder',
            'latestRental',
            'latestPayment'
        )
    );
}
}