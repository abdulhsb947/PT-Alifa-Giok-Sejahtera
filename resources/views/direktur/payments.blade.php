@extends('layouts.direktur')

@section('title', 'Laporan Pembayaran')
@section('subtitle', 'Monitoring pembayaran pelanggan')

@section('content')
    <div class="mb-6">

        <h1 class="text-2xl font-bold">
            Laporan Pembayaran
        </h1>

        <p class="text-gray-500">
            Informasi seluruh transaksi pembayaran customer.
        </p>

    </div>

    {{-- =========================== --}}
    {{-- RINGKASAN --}}
    {{-- =========================== --}}

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Total Pembayaran
            </p>

            <h2 class="text-3xl font-bold">
                {{ $totalPayment }}
            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Disetujui
            </p>

            <h2 class="text-3xl font-bold text-green-600">
                {{ $approved }}
            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Menunggu
            </p>

            <h2 class="text-3xl font-bold text-yellow-600">
                {{ $pending }}
            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Ditolak
            </p>

            <h2 class="text-3xl font-bold text-red-600">
                {{ $rejected }}
            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Total Pemasukan
            </p>

            <h2 class="text-2xl font-bold text-blue-600">
                Rp {{ number_format($totalIncome) }}
            </h2>

        </div>

    </div>

    {{-- =========================== --}}
    {{-- TABEL --}}
    {{-- =========================== --}}

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">

        <table class="min-w-full">
            

            <thead class="bg-gray-100">

<tr>

    <th class="w-16 px-4 py-3 text-center">
        No
    </th>

    <th class="w-40 px-4 py-3 text-left">
        Kode Order
    </th>

    <th class="w-72 px-4 py-3 text-left">
        Customer
    </th>

    <th class="w-36 px-4 py-3 text-center">
        Jenis Pembayaran
    </th>

    <th class="w-48 px-4 py-3 text-right">
        Nominal
    </th>

    <th class="w-40 px-4 py-3 text-center">
        Dibayarkan
    </th>

    <th class="w-40 px-4 py-3 text-center">
        Sisa Pembayaran
    </th>

    <th class="w-40 px-4 py-3 text-center">
        Status
    </th>

    <th class="w-40 px-4 py-3 text-center">
        Tanggal
    </th>

</tr>

</thead>

           <tbody>

@forelse($payments as $index => $payment)

<tr class="border-t hover:bg-blue-50 transition">

<td class="px-4 py-3 text-center font-semibold">

{{ $index+1 }}

</td>

<td class="px-4 py-3">

<div class="font-semibold">

{{ $payment->order->order_code ?? '-' }}

</div>

</td>

<td class="px-4 py-3">

{{ $payment->order->user->name ?? '-' }}

</td>

<td class="px-4 py-3">

@switch($payment->payment_type)

@case('dp')

<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

DP

</span>

@break

@case('pelunasan')

<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

Pelunasan

</span>

@break

@case('penalty')

<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

Denda

</span>

@break

@default

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

Lunas

</span>

@endswitch

</td>

<td class="px-4 py-3 text-right font-medium">

Rp {{ number_format($payment->total_tagihan) }}

</td>

<td class="px-4 py-3 text-right font-semibold text-green-600">

Rp {{ number_format($payment->amount) }}

</td>

<td class="px-4 py-3 text-right font-semibold text-red-600">

Rp {{ number_format($payment->sisa_pembayaran) }}

</td>

<td class="px-4 py-3 text-center">

@if($payment->status=='disetujui')

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

Disetujui

</span>

@elseif($payment->status=='menunggu_verifikasi')

<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

Menunggu Verifikasi

</span>

@elseif($payment->status=='menunggu_pembayaran')

<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

Menunggu Pembayaran

</span>

@else

<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

Ditolak

</span>

@endif

</td>

<td class="px-4 py-3 text-center text-gray-500">

{{ optional($payment->created_at)->format('d M Y') }}

</td>

</tr>

@empty

<tr>

<td colspan="9" class="text-center py-10 text-gray-400">

Belum ada data pembayaran.

</td>

</tr>

@endforelse

</tbody>

        </table>

    </div>

    </div>
@endsection
