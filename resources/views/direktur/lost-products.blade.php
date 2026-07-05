@extends('layouts.direktur')

@section('title', 'Laporan kehilangan Produk')
@section('subtitle', 'Monitoring kehilangan produk')

@section('content')

<div class="mb-6">

    <h1 class="text-2xl font-bold">
        Laporan Produk Hilang
    </h1>

    <p class="text-gray-500">
        Informasi kehilangan produk selama proses penyewaan scaffolding.
    </p>

</div>

{{-- =========================== --}}
{{-- RINGKASAN --}}
{{-- =========================== --}}

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Kasus
        </p>

        <h2 class="text-3xl font-bold">
            {{ $lostItems->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Produk Hilang
        </p>

        <h2 class="text-3xl font-bold text-red-600">
            {{ $totalLostQty }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Kerugian
        </p>

        <h2 class="text-2xl font-bold text-red-600">
            Rp {{ number_format($totalLostCost,0,',','.') }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Rata-rata Kerugian
        </p>

        <h2 class="text-2xl font-bold text-orange-600">

            Rp {{
                number_format(
                    $lostItems->count()
                        ? $totalLostCost / $lostItems->count()
                        : 0,
                    0,',','.'
                )
            }}

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

<th class="w-72 px-4 py-3 text-left">
Produk
</th>

<th class="w-40 px-4 py-3 text-left">
Kode Order
</th>

<th class="w-32 px-4 py-3 text-center">
Sumber
</th>

<th class="w-32 px-4 py-3 text-center">
Qty Hilang
</th>

<th class="w-48 px-4 py-3 text-right">
Kerugian
</th>

<th class="w-40 px-4 py-3 text-center">
Tanggal
</th>

</tr>

</thead>

<tbody>

@forelse($lostItems as $index => $item)

<tr class="border-t hover:bg-red-50 transition">

<td class="px-4 py-3 text-center font-semibold">

{{ $index+1 }}

</td>

<td class="px-4 py-3 font-semibold">

{{ $item->product->name ?? '-' }}

</td>

<td class="px-4 py-3">

{{ $item->order_code ?? '-' }}

</td>

<td class="px-4 py-3 text-center">

@if(($item->source ?? '') == 'Online')

<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

Online

</span>

@elseif(($item->source ?? '') == 'Offline')

<span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs">

Offline

</span>

@else

<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">

{{ $item->source ?? '-' }}

</span>

@endif

</td>

<td class="px-4 py-3 text-center font-semibold text-red-600">

{{ $item->lost_qty }}

</td>

<td class="px-4 py-3 text-right font-semibold text-red-600">

Rp {{ number_format($item->lost_cost,0,',','.') }}

</td>

<td class="px-4 py-3 text-center whitespace-nowrap">

{{ optional($item->created_at)->format('d M Y') }}

</td>

</tr>

@empty

<tr>

<td colspan="7" class="text-center py-10 text-gray-500">

Belum ada data kehilangan produk.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

@endsection