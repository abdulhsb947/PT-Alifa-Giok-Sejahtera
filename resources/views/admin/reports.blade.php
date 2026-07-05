@extends('layouts.admin')

@section('content')

@php
// 🔥 DUMMY DATA (sementara, nanti bisa dari database)
$orders = [];
$rentals = [];
$payments = [];
$maintenance = [];
$products = [];

// contoh hitungan
$totalRevenue = 0;
$totalMaintenanceCost = 0;
$activeRentals = 0;
$completedOrders = 0;
$totalStock = 0;
$rentedStock = 0;
@endphp

<div class="space-y-6">

<!-- TITLE -->
<div>
    <h1 class="text-2xl font-bold">Reports</h1>
    <p class="text-gray-500">Comprehensive operational and financial overview</p>
</div>

<!-- METRICS -->
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-sm text-gray-500 mb-2">Total Revenue</p>
        <p class="text-2xl font-bold">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-sm text-gray-500 mb-2">Active Rentals</p>
        <p class="text-2xl font-bold">{{ $activeRentals }}</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-sm text-gray-500 mb-2">Maintenance Cost</p>
        <p class="text-2xl font-bold">Rp {{ number_format($totalMaintenanceCost,0,',','.') }}</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-sm text-gray-500 mb-2">Stock Utilization</p>
        <p class="text-2xl font-bold">
            {{ $totalStock > 0 ? round(($rentedStock / $totalStock) * 100) : 0 }}%
        </p>
    </div>

</div>

<!-- STOCK TABLE -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold mb-4">Stock Report</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Product</th>
                    <th class="text-center p-2">Total</th>
                    <th class="text-center p-2">Available</th>
                    <th class="text-center p-2">Rented</th>
                    <th class="text-center p-2">Maintenance</th>
                    <th class="text-center p-2">Utilization</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr class="border-b">
                    <td class="p-2">{{ $p['name'] }}</td>
                    <td class="text-center">{{ $p['totalStock'] }}</td>
                    <td class="text-center text-green-600">{{ $p['availableStock'] }}</td>
                    <td class="text-center text-blue-600">{{ $p['rentedStock'] }}</td>
                    <td class="text-center text-yellow-600">{{ $p['maintenanceStock'] }}</td>
                    <td class="text-center">
                        {{ $p['totalStock'] > 0 ? round(($p['rentedStock'] / $p['totalStock']) * 100) : 0 }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500">
                        No data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ORDER SUMMARY -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold mb-4">Order Summary</h2>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-center">

        <div class="bg-gray-100 p-4 rounded">
            <p class="text-2xl font-bold">{{ count($orders) }}</p>
            <p class="text-sm text-gray-500">Total Orders</p>
        </div>

        <div class="bg-gray-100 p-4 rounded">
            <p class="text-2xl font-bold">
                {{ collect($orders)->where('status','waiting_verification')->count() }}
            </p>
            <p class="text-sm text-gray-500">Pending</p>
        </div>

        <div class="bg-gray-100 p-4 rounded">
            <p class="text-2xl font-bold">
                {{ collect($orders)->where('status','active_rental')->count() }}
            </p>
            <p class="text-sm text-gray-500">Active</p>
        </div>

        <div class="bg-gray-100 p-4 rounded">
            <p class="text-2xl font-bold">{{ $completedOrders }}</p>
            <p class="text-sm text-gray-500">Completed</p>
        </div>

    </div>
</div>

<!-- MAINTENANCE TABLE -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold mb-4">Maintenance Report</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="p-2">ID</th>
                    <th class="p-2">Product</th>
                    <th class="p-2">Qty</th>
                    <th class="p-2">Damage</th>
                    <th class="p-2 text-right">Cost</th>
                    <th class="p-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenance as $m)
                <tr class="border-b">
                    <td class="p-2">{{ $m['id'] }}</td>
                    <td>{{ $m['productName'] }}</td>
                    <td class="text-center">{{ $m['quantity'] }}</td>
                    <td>{{ $m['damageType'] }}</td>
                    <td class="text-right">
                        Rp {{ number_format($m['maintenanceCost'],0,',','.') }}
                    </td>
                    <td class="text-center">
                        <span class="{{ $m['status']=='under_maintenance' ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $m['status']=='under_maintenance' ? 'In Progress' : 'Done' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500">
                        No data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>

@endsection