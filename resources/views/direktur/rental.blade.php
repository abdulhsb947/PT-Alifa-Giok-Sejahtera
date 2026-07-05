@extends('layouts.direktur')

@section('title', 'Laporan Penyewaan')
@section('subtitle', 'Monitoring penyewaan produk')

@section('content')

<div class="mb-6">

    <h1 class="text-2xl font-bold">
        Laporan Penyewaan
    </h1>

    <p class="text-gray-500">
        Informasi seluruh transaksi penyewaan scaffolding.
    </p>

</div>

{{-- =========================== --}}
{{-- RINGKASAN --}}
{{-- =========================== --}}

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Penyewaan
        </p>

        <h2 class="text-3xl font-bold">
            {{ $orders->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Sedang Disewa
        </p>

        <h2 class="text-3xl font-bold text-blue-600">
            {{ $orders->where('status','disewakan')->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Selesai
        </p>

        <h2 class="text-3xl font-bold text-green-600">
            {{ $orders->where('status','selesai')->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Pendapatan
        </p>

        <h2 class="text-2xl font-bold text-green-600">

            Rp {{ number_format($totalRevenue,0,',','.') }}

        </h2>

    </div>

</div>

{{-- =========================== --}}
{{-- TABEL --}}
{{-- =========================== --}}

<div class="bg-white rounded-xl shadow overflow-hidden">

<div class="overflow-x-auto">

<table class="min-w-full table-fixed">

<thead class="bg-gray-100 text-gray-700">

<tr>

<th class="w-16 px-4 py-3 text-center">
No
</th>

<th class="w-40 px-4 py-3 text-left">
Kode Order
</th>

<th class="w-64 px-4 py-3 text-left">
Customer
</th>

<th class="w-48 px-4 py-3 text-center">
Tanggal Sewa
</th>

<th class="w-48 px-4 py-3 text-right">
Total Tagihan
</th>

<th class="w-44 px-4 py-3 text-center">
Status
</th>

</tr>

</thead>

<tbody>

@forelse($orders as $index => $order)

@php

$tagihan = $order->payments
    ->where('payment_type','tagihan')
    ->first();

@endphp

<tr class="border-t hover:bg-blue-50 transition">

<td class="px-4 py-3 text-center font-semibold">

{{ $index+1 }}

</td>

<td class="px-4 py-3 font-semibold">

{{ $order->order_code }}

</td>

<td class="px-4 py-3">

{{ $order->user->name ?? '-' }}

</td>

<td class="px-4 py-3 text-center">

{{ optional($order->created_at)->format('d M Y') }}

</td>

<td class="px-4 py-3 text-right font-semibold text-green-600">

Rp {{ number_format($tagihan->total_tagihan ?? 0,0,',','.') }}

</td>

<td class="px-4 py-3 text-center">

@switch($order->status)

@case('disewakan')

<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

Sedang Disewa

</span>

@break

@case('menunggu_pelunasan')

<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

Menunggu Pelunasan

</span>

@break

@case('menunggu_pembayaran_denda')

<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

Menunggu Denda

</span>

@break

@case('selesai')

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

Selesai

</span>

@break

@default

<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">

{{ ucwords(str_replace('_',' ',$order->status)) }}

</span>

@endswitch

</td>

</tr>

@empty

<tr>

<td colspan="6" class="text-center py-10 text-gray-500">

Belum ada data penyewaan.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

@endsection