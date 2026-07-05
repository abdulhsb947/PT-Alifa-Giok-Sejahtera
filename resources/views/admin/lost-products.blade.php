@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-bold">
        Produk Hilang
    </h1>

    {{-- SUMMARY --}}
    <div class="grid md:grid-cols-2 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Total Barang Hilang
            </p>

            <h2 class="text-3xl font-bold text-red-600">
                {{ $totalLostQty }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Total Kerugian
            </p>

            <h2 class="text-3xl font-bold text-orange-600">
                Rp {{ number_format($totalLostCost,0,',','.') }}
            </h2>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="p-5 border-b">
            <h3 class="font-bold">
                Daftar Produk Hilang
            </h3>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-100">

                    <tr>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Pelanggan</th>
                        <th class="p-3 text-left">Pesanan</th>
                        <th class="p-3 text-left">Sumber</th>
                        <th class="p-3 text-left">Qty Hilang</th>
                        <th class="p-3 text-left">Biaya</th>
                        <th class="p-3 text-left">Tanggal</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($lostItems as $item)

                    <tr class="border-b hover:bg-gray-50">

                        <td class="p-3">
                            {{ $item->product->name ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $item->customer_name ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $item->order_code ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $item->source ?? '-' }}
                        </td>

                        <td class="p-3">
                            <span class="font-semibold text-red-600">
                                {{ $item->lost_qty }}
                            </span>
                        </td>

                        <td class="p-3">
                            Rp {{ number_format($item->lost_cost,0,',','.') }}
                        </td>


                        <td class="p-3">
                            {{ $item->created_at?->format('d M Y') }}
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="6"
                            class="text-center p-6 text-gray-500">
                            Belum ada data produk hilang
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
