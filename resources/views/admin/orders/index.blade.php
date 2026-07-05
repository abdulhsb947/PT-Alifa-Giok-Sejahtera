@extends('layouts.admin')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold mb-1">Manajemen Pesanan</h1>
    <p class="text-gray-500 mb-4">Kelola semua pesanan pelanggan</p>

    <form method="GET" action="{{ url('/admin/orders') }}"
        class="bg-white p-4 rounded-xl border mb-6 flex flex-col md:flex-row gap-3">

        <!-- 🔍 PENCARIAN -->
        <div class="relative flex-1">

            <input type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Cari pesanan..."
                class="w-full border rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 outline-none">

            <button type="submit"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                </svg>

            </button>

        </div>

        <!-- 🎯 FILTER STATUS -->
        <div class="flex flex-wrap gap-2">

            <button name="status" value=""
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == '' ? 'bg-blue-600 text-white' : '' }}">
                Semua
            </button>

            <button name="status" value="menunggu_verifikasi"
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == 'menunggu_verifikasi' ? 'bg-blue-600 text-white' : '' }}">
                Menunggu Verifikasi
            </button>

            <button name="status" value="review_lapangan"
                class="px-3 py-1 border rounded text-sm
{{ request('status') == 'review_lapangan' ? 'bg-blue-600 text-white' : '' }}">
                Review Lapangan
            </button>

            <button name="status" value="disetujui"
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == 'disetujui' ? 'bg-blue-600 text-white' : '' }}">
                Disetujui
            </button>

            <button name="status" value="ditolak"
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == 'ditolak' ? 'bg-blue-600 text-white' : '' }}">
                Ditolak
            </button>

            <button name="status" value="menunggu_pembayaran"
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == 'menunggu_pembayaran' ? 'bg-blue-600 text-white' : '' }}">
                Menunggu Pembayaran
            </button>

            <button name="status" value="aktif_disewa"
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == 'aktif_disewa' ? 'bg-blue-600 text-white' : '' }}">
                Sedang Disewa
            </button>

            <button name="status" value="selesai"
                class="px-3 py-1 border rounded text-sm 
{{ request('status') == 'selesai' ? 'bg-blue-600 text-white' : '' }}">
                Selesai
            </button>

        </div>

    </form>

    <!-- LIST PESANAN -->
    <div class="space-y-4">

        @foreach($orders as $order)

        <div class="border rounded-xl p-4 shadow-sm bg-white">

            <div class="flex justify-between items-center mb-2">
                <h2 class="font-bold text-lg">{{ $order->order_code }}</h2>

                <span class="px-3 py-1 text-sm rounded-full

@if($order->status == 'menunggu_hasil_survey')
    bg-orange-100 text-orange-700

@elseif($order->status == 'review_lapangan')
    bg-blue-100 text-blue-700

@elseif($order->status == 'menunggu_verifikasi')
    bg-yellow-100 text-yellow-700

@elseif($order->status == 'disetujui')
    bg-green-100 text-green-700

@elseif($order->status == 'ditolak')
    bg-red-100 text-red-700

@elseif($order->status == 'menunggu_persetujuan_pelanggan')
    bg-purple-100 text-purple-700

@elseif($order->status == 'perjanjian_disetujui')
    bg-indigo-100 text-indigo-700

@elseif($order->status == 'perjanjian_ditolak')
    bg-red-100 text-red-700

@elseif($order->status == 'telah_dibayar')
    bg-emerald-100 text-emerald-700

@elseif($order->status == 'sewa_telah_berlaku')
    bg-green-100 text-green-700

@elseif($order->status == 'menunggu_pelunasan')
    bg-yellow-100 text-yellow-700

@elseif($order->status == 'menunggu_pembayaran_denda')
    bg-red-100 text-red-700

@elseif($order->status == 'menunggu_pelunasan_dan_denda')
    bg-red-100 text-red-700

@elseif($order->status == 'selesai')
    bg-gray-200 text-gray-700

@else
    bg-gray-100 text-gray-500

@endif

">
                    {{ ucfirst(str_replace('_',' ',$order->status)) }}
                </span>
            </div>

            <div class="grid md:grid-cols-4 gap-4 text-sm text-gray-600">

                <div>
                    <p class="font-semibold text-gray-800">Proyek</p>
                    <p>{{ $order->project_name }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-800">Pelanggan</p>
                    <p>{{ $order->user->name }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-800">Produk</p>
                    <p>{{ $order->items->count() }} item</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-800">Durasi</p>
                    <p>{{ $order->duration }} {{ $order->duration_unit }}</p>
                </div>

            </div>

            <div class="mt-4 flex justify-end">
                <a href="/admin/orders/{{ $order->id }}"
                    class="border border-blue-500 text-blue-500 px-4 py-1 rounded-lg hover:bg-blue-500 hover:text-white">
                    Lihat Detail
                </a>
            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection