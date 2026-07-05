@extends('layouts.customer')

@section('content')

@php
$orders = [
    [
        'id' => 'ORD-001',
        'projectName' => 'Tower A',
        'productName' => 'Frame Scaffolding',
        'quantity' => 10,
        'rentalDuration' => 7,
        'durationUnit' => 'Days',
        'status' => 'waiting_payment',
        'createdAt' => '2024-01-15'
    ],
    [
        'id' => 'ORD-002',
        'projectName' => 'Mall Project',
        'productName' => 'Ringlock',
        'quantity' => 5,
        'rentalDuration' => 14,
        'durationUnit' => 'Days',
        'status' => 'completed',
        'createdAt' => '2024-01-10'
    ],
];

$payments = [
    ['orderId' => 'ORD-001'],
    ['orderId' => 'ORD-001'],
    ['orderId' => 'ORD-002'],
];

$statusConfig = [
    'waiting_verification' => ['label' => 'Waiting Verification', 'color' => 'bg-gray-200 text-gray-600'],
    'waiting_customer_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-yellow-100 text-yellow-600'],
    'revision_by_admin' => ['label' => 'Revision', 'color' => 'bg-blue-100 text-blue-600'],
    'approved' => ['label' => 'Approved', 'color' => 'bg-green-100 text-green-600'],
    'waiting_payment' => ['label' => 'Awaiting Payment', 'color' => 'bg-blue-100 text-blue-600'],
    'payment_verified' => ['label' => 'Payment Verified', 'color' => 'bg-green-100 text-green-600'],
    'active_rental' => ['label' => 'Active Rental', 'color' => 'bg-blue-100 text-blue-600'],
    'completed' => ['label' => 'Completed', 'color' => 'bg-green-100 text-green-600'],
    'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-red-100 text-red-600'],
];

// SORT DESC (seperti React)
usort($orders, function($a, $b){
    return strtotime($b['createdAt']) - strtotime($a['createdAt']);
});
@endphp

<div class="container mx-auto px-4 py-8 space-y-6">

<!-- TITLE -->
<div>
    <h1 class="text-2xl md:text-3xl font-bold">Transaction History</h1>
    <p class="text-gray-500">Complete history of all your transactions and orders</p>
</div>

<!-- LIST -->
<div class="space-y-4">

@if(count($orders) == 0)

<div class="bg-white border rounded-xl p-12 text-center">
    <i data-lucide="file-text" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
    <h3 class="font-bold mb-2">No transaction history</h3>
    <p class="text-gray-500">Your transactions will appear here</p>
</div>

@else

@foreach($orders as $order)

@php
$status = $statusConfig[$order['status']] ?? ['label' => $order['status'], 'color' => 'bg-gray-200 text-gray-600'];
$orderPayments = array_filter($payments, fn($p) => $p['orderId'] == $order['id']);
@endphp

<div class="bg-white border rounded-xl p-6">

    <div class="flex justify-between items-center mb-3">
        <span class="font-bold text-lg">{{ $order['id'] }}</span>
        <span class="px-3 py-1 rounded text-sm {{ $status['color'] }}">
            {{ $status['label'] }}
        </span>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm mb-3">

        <div>
            <span class="text-gray-400">Project:</span>
            <p class="font-medium">{{ $order['projectName'] }}</p>
        </div>

        <div>
            <span class="text-gray-400">Product:</span>
            <p class="font-medium">{{ $order['productName'] }}</p>
        </div>

        <div>
            <span class="text-gray-400">Quantity:</span>
            <p class="font-medium">{{ $order['quantity'] }} units</p>
        </div>

        <div>
            <span class="text-gray-400">Duration:</span>
            <p class="font-medium">{{ $order['rentalDuration'] }} {{ $order['durationUnit'] }}</p>
        </div>

    </div>

    <div class="flex items-center gap-3 text-sm text-gray-500">
        <i data-lucide="clock" class="w-4 h-4"></i>
        <span>Created: {{ date('d M Y', strtotime($order['createdAt'])) }}</span>

        @if(count($orderPayments) > 0)
            <span>•</span>
            <span>Payments: {{ count($orderPayments) }}</span>
        @endif
    </div>

</div>

@endforeach

@endif

</div>

</div>

@endsection