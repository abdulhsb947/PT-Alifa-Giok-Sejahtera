@extends('layouts.direktur')

@section('title', 'Laporan Pemesanan')
@section('subtitle', 'Monitoring pemesanan produk')

@section('content')

    <div class="mb-6">

        <h1 class="text-2xl font-bold">

            Laporan Pemesanan

        </h1>

        <p class="text-gray-500">

            Informasi seluruh data pemesanan scaffolding.

        </p>

    </div>

    {{-- =========================== --}}
    {{-- RINGKASAN --}}
    {{-- =========================== --}}

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">

                Total Pemesanan

            </p>

            <h2 class="text-3xl font-bold">

                {{ $totalOrder }}

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

                Disetujui

            </p>

            <h2 class="text-3xl font-bold text-blue-600">

                {{ $approved }}

            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">

                Selesai

            </p>

            <h2 class="text-3xl font-bold text-green-600">

                {{ $finished }}

            </h2>

        </div>

    </div>

    {{-- =========================== --}}
    {{-- TABEL --}}
    {{-- =========================== --}}

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

                    <th class="w-72 px-4 py-3 text-left">
                        Produk
                    </th>

                    <th class="w-24 px-4 py-3 text-center">
                        Unit
                    </th>

                    <th class="w-44 px-4 py-3 text-center">
                        Status
                    </th>

                    <th class="w-40 px-4 py-3 text-center">
                        Tanggal
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($orders as $index => $order)

                <tr class="border-t hover:bg-blue-50 transition">

                    <td class="px-4 py-3 text-center font-semibold">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-4 py-3 font-semibold">
                        {{ $order->order_code }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $order->user->name }}
                    </td>

                    <td class="px-4 py-3">

                        <div class="flex flex-wrap gap-1">

                            @foreach($order->items as $item)

                                <span class="bg-gray-100 px-2 py-1 rounded text-xs">

                                    {{ $item->product->name }}

                                </span>

                            @endforeach

                        </div>

                    </td>

                    <td class="px-4 py-3 text-center font-semibold">

                        {{ $order->items->sum('quantity') }}

                    </td>

                    <td class="px-4 py-3 text-center">

                        @switch($order->status)

                            @case('menunggu')

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

                                    Menunggu

                                </span>

                            @break

                            @case('review_lapangan')

                                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs">

                                    Review Lapangan

                                </span>

                            @break

                            @case('disetujui')

                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

                                    Disetujui

                                </span>

                            @break

                            @case('perjanjian_disetujui')

                                <span class="bg-cyan-100 text-cyan-700 px-3 py-1 rounded-full text-xs">

                                    Perjanjian Disetujui

                                </span>

                            @break

                            @case('telah_dibayar')

                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs">

                                    Telah Dibayar

                                </span>

                            @break

                            @case('disewakan')

                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs">

                                    Disewakan

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

                    <td class="px-4 py-3 text-center whitespace-nowrap">

                        {{ $order->created_at->format('d M Y') }}

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="py-10 text-center text-gray-500">

                        Belum ada data pemesanan.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

    @endsection
