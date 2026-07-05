<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function orders()
    {
        $orders = Order::with([
            'user',
            'items.product'
        ])
        ->latest()
        ->get();

        return view(
            'direktur.orders',
            [

                'orders' => $orders,

                'totalOrder' => $orders->count(),

                'pending' => $orders->where('status','menunggu')->count(),

                'approved' => $orders->where('status','disetujui')->count(),

                'finished' => $orders->where('status','selesai')->count()

            ]
        );
    }
}