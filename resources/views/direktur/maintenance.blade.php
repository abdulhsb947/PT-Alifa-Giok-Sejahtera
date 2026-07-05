@extends('layouts.direktur')

@section('title', 'Laporan Perawatan')
@section('subtitle', 'Monitoring perawatan produk')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-3xl font-bold">
            Laporan Perawatan
        </h1>

        <p class="text-gray-500">
            Monitoring seluruh aktivitas perawatan produk
        </p>
    </div>

    {{-- SUMMARY --}}
    <div class="grid md:grid-cols-3 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Sedang Diproses
            </p>

            <h2 class="text-3xl font-bold text-yellow-600">
                {{ $inProgress }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Selesai
            </p>

            <h2 class="text-3xl font-bold text-green-600">
                {{ $completed }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Total Perawatan
            </p>

            <h2 class="text-3xl font-bold text-blue-600">
                {{ $maintenances->count() }}
            </h2>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold">
                Data Perawatan Produk
            </h3>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="px-4 py-3 text-left">
                            Produk
                        </th>

                        <th class="px-4 py-3 text-left">
                            Qty
                        </th>

                        <th class="px-4 py-3 text-left">
                            Biaya
                        </th>

                        <th class="px-4 py-3 text-left">
                            Catatan
                        </th>

                        <th class="px-4 py-3 text-left">
                            Tanggal Masuk
                        </th>

                        <th class="px-4 py-3 text-left">
                            Tanggal Selesai
                        </th>

                        <th class="px-4 py-3 text-center">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($maintenances as $m)

                    <tr class="border-b hover:bg-gray-50">

                        <td class="px-4 py-3 font-medium">
                            {{ $m->product->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $m->qty }}
                        </td>

                        <td class="px-4 py-3 text-blue-600 font-semibold">
                            Rp {{ number_format($m->price, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $m->notes ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $m->created_at?->format('d M Y') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $m->status == 'selesai'
                                ? $m->updated_at?->format('d M Y')
                                : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">

                            @if($m->status == 'proses')

                            <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                Proses
                            </span>

                            @elseif($m->status == 'selesai')

                            <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                Selesai
                            </span>

                            @else

                            <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                {{ ucfirst($m->status) }}
                            </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="text-center py-8 text-gray-500">
                            Belum ada data perawatan
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection