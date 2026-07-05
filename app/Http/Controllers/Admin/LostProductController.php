<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineOrderItem;
use App\Models\ReturnItem;
use Illuminate\Http\Request;

class LostProductController extends Controller
{
    public function index()
    {
        $onlineLostItems = ReturnItem::with([
            'product',
            'rental.order.user'
        ])
        ->where('lost_qty', '>', 0)
        ->latest()
        ->get();

        $offlineLostItems = OfflineOrderItem::with([
            'product',
            'order'
        ])
        ->where('lost_qty', '>', 0)
        ->latest()
        ->get();

        $lostItems = $onlineLostItems
            ->map(function ($item) {
                return (object) [
                    'product' => $item->product,
                    'customer_name' => $item->rental->order->user->name ?? '-',
                    'order_code' => $item->rental->order->order_code ?? '-',
                    'source' => 'Online',
                    'lost_qty' => $item->lost_qty,
                    'lost_cost' => $item->lost_cost,
                    'created_at' => $item->created_at,
                ];
            })
            ->merge(
                $offlineLostItems->map(function ($item) {
                    return (object) [
                        'product' => $item->product,
                        'customer_name' => $item->order->customer_name ?? '-',
                        'order_code' => $item->order->order_code ?? '-',
                        'source' => 'Offline',
                        'lost_qty' => $item->lost_qty,
                        'lost_cost' => $item->lost_cost,
                        'created_at' => $item->updated_at,
                    ];
                })
            )
            ->sortByDesc('created_at')
            ->values();

        $totalLostQty = $lostItems->sum('lost_qty');

        $totalLostCost = $lostItems->sum('lost_cost');

        return view(
            'admin.lost-products',
            compact(
                'lostItems',
                'totalLostQty',
                'totalLostCost'
            )
        );
    }
}
