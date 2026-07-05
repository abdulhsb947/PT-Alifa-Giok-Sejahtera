<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Product;
use App\Models\Maintenance;
use App\Models\ReturnItem;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();

        $pendingOrders = Order::whereIn('status', [
            'menunggu_verifikasi',
            'review_lapangan'
        ])->count();

        $activeRentals = Rental::whereIn('status', [
            'disewakan',
            'menunggu_konfirmasi_pengembalian'
        ])->count();

        $totalRevenue = Payment::where('status', 'disetujui')
            ->sum('amount');

        $pendingPayments = Payment::where(
            'status',
            'menunggu_verifikasi'
        )->count();

        $pendingPenalty = Payment::where(
            'payment_type',
            'penalty'
        )
            ->where(
                'status',
                'menunggu_verifikasi'
            )
            ->count();

        $maintenanceCount = Maintenance::where(
            'status',
            'proses'
        )->count();

        $totalStock = Product::sum('total_stock');
        $availableStock = Product::sum('available_stock');
        $rentedStock = Product::sum('rented_stock');
        $maintenanceStock = Product::sum('maintenance_stock');

        $latestOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $latestPayments = Payment::with('order.user')
            ->latest()
            ->take(5)
            ->get();

        $lateRentals = Rental::with([
            'order',
            'penalty'
        ])
            ->whereHas('penalty')
            ->latest()
            ->take(5)
            ->get();

        $lostProductCount = ReturnItem::sum('lost_qty');

        $lostProductCost = ReturnItem::sum('lost_cost');

        return view(
            'admin.dashboard',
            compact(
                'totalOrders',
                'pendingOrders',
                'activeRentals',
                'totalRevenue',
                'pendingPayments',
                'pendingPenalty',
                'maintenanceCount',
                'totalStock',
                'availableStock',
                'rentedStock',
                'maintenanceStock',
                'latestOrders',
                'latestPayments',
                'lateRentals',
                'lostProductCount'
            )
        );
    }
}
