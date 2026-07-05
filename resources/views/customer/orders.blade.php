@extends('layouts.customer')

@section('content')

<div class="max-w-6xl mx-auto p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold">Pesanan Saya</h1>
            <p class="text-gray-500 text-sm">
                Lihat dan kelola pesanan scaffolding Anda
            </p>
        </div>

        <a href="#"
   onclick="openOrderModal()"
   class="bg-blue-600 text-white px-4 py-2 rounded flex items-center gap-2 w-fit">

    + Buat Pesanan

</a>

    </div>

    <!-- FILTER -->
    <form method="GET" action="{{ url('/customer/orders') }}"
        class="bg-white p-4 rounded-xl border mb-6 flex flex-col md:flex-row gap-3">

        <!-- SEARCH -->
        <div class="relative flex-1">
            <input type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Cari pesanan..."
                class="w-full border rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 outline-none">

            <button type="submit"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600">
                🔍
            </button>
        </div>

        <!-- STATUS -->
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

            <button name="status" value="menunggu_persetujuan"
                class="px-3 py-1 border rounded text-sm 
                {{ request('status') == 'menunggu_persetujuan' ? 'bg-blue-600 text-white' : '' }}">
                Menunggu Persetujuan
            </button>

            <button name="status" value="menunggu_pembayaran"
                class="px-3 py-1 border rounded text-sm 
                {{ request('status') == 'menunggu_pembayaran' ? 'bg-blue-600 text-white' : '' }}">
                Menunggu Pembayaran
            </button>

            <button name="status" value="sewa_telah_berlaku"
                class="px-3 py-1 border rounded text-sm 
                {{ request('status') == 'aktif_disewa' ? 'bg-blue-600 text-white' : '' }}">
                Sewa telah berlaku
            </button>

            <button name="status" value="selesai"
                class="px-3 py-1 border rounded text-sm 
                {{ request('status') == 'selesai' ? 'bg-blue-600 text-white' : '' }}">
                Selesai
            </button>

        </div>

    </form>

</div>

<!-- LIST -->
<div class="max-w-6xl mx-auto p-6 space-y-4">

    @forelse($orders as $order)

    <div class="bg-white border rounded-xl p-4 md:p-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">

        <div class="space-y-2 flex-1">

            <!-- HEADER -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold">{{ $order->order_code }}</span>

                @php
                $statusColor = match($order->status) {
                'menunggu_hasil_survey' => 'bg-orange-200 text-orange-700',
                'menunggu_verifikasi' => 'bg-gray-200 text-gray-700',
                'menunggu_persetujuan' => 'bg-blue-100 text-blue-600',
                'menunggu_pembayaran' => 'bg-yellow-100 text-yellow-600',

                // 🔥 FIX
                'sewa_telah_berlaku', 'rented' => 'bg-green-100 text-green-600',

                'selesai' => 'bg-gray-300 text-gray-800',
                'ditolak' => 'bg-red-100 text-red-600',
                default => 'bg-gray-100'
                };

                $statusLabel = match($order->status){

'menunggu_hasil_survey'
=>'Menunggu Hasil Survey',

'menunggu_verifikasi'
=>'Menunggu Verifikasi',

'menunggu_persetujuan'
=>'Menunggu Persetujuan',

'review_lapangan'
=>'Review Lapangan',

'disetujui'
=>'Disetujui',

'menunggu_pembayaran'
=>'Menunggu Pembayaran',

'perjanjian_disetujui'
=>'Perjanjian Disetujui',

'telah_dibayar'
=>'Telah Dibayar',

'disewakan',
'sewa_telah_berlaku',
'rented'
=>'Sedang Disewa',

'menunggu_pelunasan'
=>'Menunggu Pelunasan',

'menunggu_pembayaran_denda'
=>'Menunggu Pembayaran Denda',

'selesai'
=>'Selesai',

'ditolak'
=>'Ditolak',

default
=>ucwords(str_replace('_',' ',$order->status))

};
                
                @endphp

                <span class="text-xs px-2 py-1 rounded {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <!-- DETAIL -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">

                <div>
                    <p class="text-gray-400">Proyek</p>
                    <p class="font-medium">{{ $order->project_name }}</p>
                </div>

                <div>
                    <p class="text-gray-400">Jumlah Produk</p>
                    <p class="font-medium">
                        {{ $order->items_count ?? '-' }} item
                    </p>
                </div>

                <div>
                    <p class="text-gray-400">Durasi</p>
                    <p class="font-medium">{{ $order->duration }} bulan</p>
                </div>

                <div>
                    <p class="text-gray-400">Dibuat</p>
                    <p class="font-medium">
                        {{ $order->created_at->format('d M Y') }}
                    </p>
                </div>

            </div>

        </div>

        <!-- BUTTON -->
        <a href="/customer/orders/{{ $order->id }}"
            class="border border-blue-600 text-blue-600 px-4 py-2 rounded flex items-center gap-2 justify-center hover:bg-blue-50">
            👁 Lihat Detail
        </a>

    </div>

    @empty

    <div class="text-center py-10">
        <p class="text-gray-400">Belum ada pesanan</p>
    </div>

    @endforelse

</div>


<!-- MODAL KONFIRMASI -->
<div id="orderModal"
     class="fixed inset-0 bg-black/50 hidden
            items-center justify-center
            z-50 px-4">

    <!-- CARD -->
    <div class="bg-white rounded-3xl
                w-full max-w-2xl
                p-6 md:p-8
                shadow-2xl
                animate-fadeIn">

        <!-- HEADER -->
        <div class="text-center mb-6">

            <div class="w-16 h-16 md:w-20 md:h-20
                        mx-auto mb-4
                        bg-blue-100
                        text-blue-600
                        rounded-full
                        flex items-center justify-center
                        text-3xl">

                📄

            </div>

            <h2 class="text-2xl md:text-3xl
                       font-bold text-gray-800">

                Konfirmasi Pesanan

            </h2>

            <p class="text-gray-500 mt-2
                      text-sm md:text-base">

                Mohon perhatikan informasi berikut
                sebelum melanjutkan pesanan.

            </p>

        </div>

        <!-- CONTENT -->
        <div class="space-y-5 text-gray-700">

            <!-- DOKUMEN -->
            <div class="bg-gray-50 rounded-2xl p-4">

                <h3 class="font-semibold
                           text-lg mb-3">

                    Dokumen yang perlu disiapkan

                </h3>

                <ul class="space-y-2 text-sm md:text-base">

                    <li class="flex items-center gap-2">
                        ✅ NPWP Perusahaan
                    </li>

                    <li class="flex items-center gap-2">
                        ✅ SPK / Surat Kerja Proyek
                    </li>

                    <li class="flex items-center gap-2">
                        ✅ KTP Penanggung Jawab
                    </li>

                </ul>

            </div>

            <!-- INFORMASI -->
            <div class="bg-yellow-50
                        border border-yellow-200
                        rounded-2xl p-4">

                <h3 class="font-semibold
                           text-lg mb-2
                           text-yellow-700">

                    Informasi Penting

                </h3>

                <p class="text-sm md:text-base leading-relaxed">

                    Harga dan kebutuhan scaffolding
                    yang diajukan masih bersifat
                    estimasi sementara.

                    Tim kami akan melakukan
                    pengecekan dan survey langsung
                    sesuai kondisi lapangan proyek.

                </p>

            </div>

        </div>

        <!-- BUTTON -->
        <div class="flex flex-col md:flex-row
                    justify-center gap-3
                    mt-8">

            <!-- BATAL -->
            <button onclick="closeOrderModal()"
                class="w-full md:w-auto
                       bg-red-500 hover:bg-red-600
                       text-white px-6 py-3
                       rounded-xl
                       font-medium
                       transition">

                Tidak

            </button>

            <!-- LANJUT -->
            <a href="/customer/orders/create"
               class="w-full md:w-auto
                      text-center
                      bg-blue-600 hover:bg-blue-700
                      text-white px-6 py-3
                      rounded-xl
                      font-medium
                      transition">

                Ya, Lanjutkan

            </a>

        </div>

    </div>

</div>



<script>

function openOrderModal()
{
    document
        .getElementById('orderModal')
        .classList
        .remove('hidden');

    document
        .getElementById('orderModal')
        .classList
        .add('flex');
}

function closeOrderModal()
{
    document
        .getElementById('orderModal')
        .classList
        .remove('flex');

    document
        .getElementById('orderModal')
        .classList
        .add('hidden');
}

</script>

@endsection