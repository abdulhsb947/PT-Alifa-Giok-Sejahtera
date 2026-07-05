@extends('layouts.direktur')

@section('title', 'Laporan Stok Produk')
@section('subtitle', 'Monitoring Stok Produk')

@section('content')

<div class="mb-6">

    <h1 class="text-2xl font-bold">
        Laporan Stok Barang
    </h1>

    <p class="text-gray-500">
        Informasi ketersediaan stok scaffolding perusahaan.
    </p>

</div>

{{-- =========================== --}}
{{-- RINGKASAN --}}
{{-- =========================== --}}

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Produk
        </p>

        <h2 class="text-3xl font-bold">
            {{ $products->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Stok Tersedia
        </p>

        <h2 class="text-3xl font-bold text-green-600">
            {{ number_format($totalStock) }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Sedang Disewa
        </p>

        <h2 class="text-3xl font-bold text-blue-600">
            {{ number_format($totalRented) }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Seluruh Stok
        </p>

        <h2 class="text-3xl font-bold text-purple-600">
            {{ number_format($totalStock + $totalRented) }}
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

                    <th class="w-80 px-4 py-3 text-left">
                        Nama Produk
                    </th>

                    <th class="w-40 px-4 py-3 text-center">
                        Tersedia
                    </th>

                    <th class="w-40 px-4 py-3 text-center">
                        Disewa
                    </th>

                    <th class="w-40 px-4 py-3 text-center">
                        Total
                    </th>

                    <th class="w-48 px-4 py-3 text-center">
                        Status
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($products as $index => $product)

                <tr class="border-t hover:bg-blue-50 transition">

                    <td class="px-4 py-3 text-center font-semibold">

                        {{ $index + 1 }}

                    </td>

                    <td class="px-4 py-3 font-semibold">

                        {{ $product->name }}

                    </td>

                    <td class="px-4 py-3 text-center text-green-600 font-semibold">

                        {{ $product->available_stock }}

                    </td>

                    <td class="px-4 py-3 text-center text-blue-600 font-semibold">

                        {{ $product->rented_stock }}

                    </td>

                    <td class="px-4 py-3 text-center font-semibold">

                        {{ $product->available_stock + $product->rented_stock }}

                    </td>

                    <td class="px-4 py-3 text-center">

                        @if($product->available_stock == 0)

                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

                                Stok Habis

                            </span>

                        @elseif($product->available_stock <= 5)

                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

                                Hampir Habis

                            </span>

                        @else

                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

                                Aman

                            </span>

                        @endif

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="6" class="text-center py-10 text-gray-500">

                        Belum ada data produk.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection