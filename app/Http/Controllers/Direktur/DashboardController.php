<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Maintenance;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();

        $activeRentals = Rental::whereNotIn('status', [
            'selesai'
        ])->count();

        $totalProducts = Product::count();

        $maintenanceProducts = \App\Models\Maintenance::where(
    'status',
    'proses'
)->count();

// Biaya perawatan
$maintenanceCost = Maintenance::sum('price');

// Kerugian barang hilang
$lostCost = ReturnItem::sum('lost_cost');

// Total kerugian
$totalLoss = $maintenanceCost + $lostCost;

        $totalRevenue = Payment::where('status', 'disetujui')
            ->whereIn('payment_type', [
                'dp',
                'lunas',
                'pelunasan',
                'penalty'
            ])
            ->sum('amount');

        $pendingPayments = Payment::where(
            'status',
            'menunggu_verifikasi'
        )->count();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();


        $totalLostProducts = ReturnItem::where(
    'lost_qty',
    '>',
    0
)->sum('lost_qty');    


$ordersChart = Order::selectRaw("
        DATE_FORMAT(created_at,'%b') as month,
        COUNT(*) as total
    ")
    ->groupBy('month')
    ->orderByRaw('MIN(created_at)')
    ->get();

$financeChart = collect([
    [
        'month' => 'Saat Ini',
        'revenue' => $totalRevenue,
        'loss' => $totalLoss ?? 0
    ]
]);

$totalLostProducts = ReturnItem::sum('lost_qty');

$maintenanceCost = Maintenance::sum('price');

$lostCost = ReturnItem::sum('lost_cost');

$totalLoss = $maintenanceCost + $lostCost;

$orderChart = Order::selectRaw("
    MONTH(created_at) as month,
    COUNT(*) as total
")
->groupBy('month')
->orderBy('month')
->get();

$rentalStatus = [
    Rental::where('status','dikirim')->count(),
    Rental::where('status','berjalan')->count(),
    Rental::where('status','selesai')->count(),
];

$topProducts = Product::orderByDesc('rented_stock')
    ->take(5)
    ->get();

        return view(
            'direktur.dashboard',
            compact(
                'totalOrders',
                'activeRentals',
                'totalProducts',
                'maintenanceProducts',
                'totalRevenue',
                'pendingPayments',
                'recentOrders',
                'totalLostProducts',
                'maintenanceCost',
        'lostCost',
        'totalLoss',
        'ordersChart',
'financeChart',
'totalLostProducts',
'maintenanceCost',
'lostCost',
'totalLoss',
'orderChart',
'rentalStatus',
'topProducts'
            )
        );
    }
}