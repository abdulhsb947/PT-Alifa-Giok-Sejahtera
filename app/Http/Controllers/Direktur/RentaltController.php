<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class RentaltController extends Controller
{
    public function index()
{
    $orders = Order::with([
        'user',
        'items.product',
        'rental',
        'payments'
    ])
    ->latest()
    ->get();

    $totalRevenue = 0;

    foreach ($orders as $order) {

        $tagihan = $order->payments
            ->where('payment_type', 'tagihan')
            ->first();

        if ($tagihan) {
            $totalRevenue += $tagihan->total_tagihan;
        }
    }

    return view(
        'direktur.rental',
        compact(
            'orders',
            'totalRevenue'
        )
    );
}
}
