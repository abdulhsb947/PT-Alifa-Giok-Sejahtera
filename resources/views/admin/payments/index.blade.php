@extends('layouts.admin')

@section('content')

<div class="p-6 space-y-6">

    <!-- JUDUL -->
    <div>
        <h1 class="text-2xl font-bold">Manajemen Pembayaran</h1>
        <p class="text-gray-500 text-sm">
            Verifikasi pembayaran pelanggan (pesanan & denda)
        </p>
    </div>

    {{-- ===================== --}}
    {{-- RINGKASAN --}}
    {{-- ===================== --}}
    @php
    $menunggu = $payments->where('status', 'menunggu_verifikasi')->count();
    $disetujui = $payments->where('status', 'disetujui')->count();
    $total = $payments->where('status', 'disetujui')->sum('amount');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-yellow-100 p-3 rounded-lg">⏳</div>
            <div>
                <h3 class="text-xl font-bold">{{ $menunggu }}</h3>
                <p class="text-gray-500 text-sm">Menunggu Verifikasi</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-green-100 p-3 rounded-lg">✅</div>
            <div>
                <h3 class="text-xl font-bold">{{ $disetujui }}</h3>
                <p class="text-gray-500 text-sm">Disetujui</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-red-100 p-3 rounded-lg">⚠️</div>
            <div>
                <h3 class="text-xl font-bold">{{ $penaltiesPending ?? 0 }}</h3>
                <p class="text-gray-500 text-sm">Denda Menunggu</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-orange-100 p-3 rounded-lg">💰</div>
            <div>
                <h3 class="text-xl font-bold">
                    Rp {{ number_format($total) }}
                </h3>
                <p class="text-gray-500 text-sm">Total Disetujui</p>
            </div>
        </div>

    </div>

    {{-- ===================== --}}
    {{-- LIST PEMBAYARAN --}}
    {{-- ===================== --}}
    <div class="space-y-4">

        @foreach($payments as $payment)
        @php
$hasPayment = $payment->order->payments
    ->whereIn('payment_type', ['dp', 'pelunasan', 'lunas'])
    ->count() > 0;
@endphp

@if($payment->payment_type == 'tagihan' && $hasPayment)
    @continue
@endif

        <div class="bg-white rounded-xl shadow px-6 py-5">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                <!-- KIRI -->
                <div class="flex-1 space-y-3">

                    <!-- HEADER -->
                    <div class="flex flex-wrap items-center gap-3">

                        <h5 class="font-bold text-lg">
                            PAY-{{ str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}
                        </h5>

                        <!-- STATUS -->
                        <span class="px-3 py-1 text-xs rounded-full
                        @if($payment->status == 'disetujui') bg-green-100 text-green-600
                        @elseif($payment->status == 'menunggu_verifikasi') bg-yellow-100 text-yellow-600
                        @else bg-red-100 text-red-600
                        @endif
                    ">
                            {{ ucfirst(str_replace('_',' ',$payment->status)) }}
                        </span>

                        <!-- TIPE -->
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600">

    @if($payment->payment_type == 'dp')
        💰 DP
    @elseif($payment->payment_type == 'lunas')
        ✅ Lunas
    @elseif($payment->payment_type == 'pelunasan')
        📌 Pelunasan
    @elseif($payment->payment_type == 'penalty')
        ⚠ Denda
    @endif

</span>
                    </div>

                    <!-- DATA -->
                    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-6 text-sm">

                        <!-- PESANAN -->
                        <div>
                            <p class="text-gray-400 mb-1">Pesanan</p>
                            <p class="font-semibold">
                                {{ $payment->order->order_code ?? '-' }}
                            </p>
                            <p class="text-gray-500 text-xs">
                                {{ $payment->order->project_name ?? '' }}
                            </p>
                        </div>

                        <!-- PELANGGAN -->
                        <div>
                            <p class="text-gray-400 mb-1">Pelanggan</p>
                            <p class="font-semibold">
                                {{ $payment->order->user->name ?? '-' }}
                            </p>
                        </div>

                        <!-- TOTAL TAGIHAN -->
@if($payment->payment_type == 'penalty')

<div>
    <p class="text-gray-400 mb-1">
        Total Denda
    </p>

    <p class="font-semibold text-red-600">
        Rp {{ number_format($payment->amount) }}
    </p>
</div>

<div>
    <p class="text-gray-400 mb-1">
        Status Denda
    </p>

    <p class="font-semibold
            @if($payment->status == 'disetujui')
                text-green-600
            @elseif($payment->status == 'menunggu_verifikasi')
                text-yellow-600
            @else
                text-red-600
            @endif">
        {{ ucfirst(str_replace('_',' ',$payment->status)) }}
    </p>
</div>

<div>
    <p class="text-gray-400 mb-1">
        Bukti Bayar
    </p>

    <p class="font-semibold">
        {{ $payment->proof ? 'Sudah Upload' : 'Belum Upload' }}
    </p>
</div>

@else

<div>
    <p class="text-gray-400 mb-1">
        Total Tagihan
    </p>

    <p class="font-semibold text-blue-600">
        Rp {{ number_format($payment->total_tagihan ?? 0) }}
    </p>
</div>

<div>
    <p class="text-gray-400 mb-1">
        Sudah Dibayar
    </p>

    <p class="font-semibold text-green-600">
        Rp {{ number_format($payment->amount ?? 0) }}
    </p>
</div>

<div>
    <p class="text-gray-400 mb-1">
        Sisa Pembayaran
    </p>

    <p class="font-semibold text-red-600">
        Rp {{ number_format($payment->sisa_pembayaran ?? 0) }}
    </p>
</div>

@endif

                        <!-- TANGGAL -->
                        <div>
                            <p class="text-gray-400 mb-1">Tanggal</p>
                            <p class="font-semibold">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                            </p>
                        </div>
                        

                        <!-- BUKTI -->
                        <div>
                            <p class="text-gray-400 mb-1">Bukti</p>

                            @if($payment->proof)
                            <a href="{{ asset('storage/'.$payment->proof) }}"
                                target="_blank"
                                class="text-blue-600 text-sm underline">
                                Lihat
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">Tidak ada</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-gray-400 mb-1">Catatan</p>
                            <p class="font-semibold text-gray-700">
                                {{ $payment->notes ?: '-' }}
                            </p>
                        </div>

                    </div>

                </div>

                <!-- KANAN (AKSI) -->
                <div class="flex gap-2 justify-start lg:justify-end">

                    @if($payment->status == 'menunggu_verifikasi')

                    <form method="POST" action="/admin/payments/{{ $payment->id }}/approve">
                        @csrf
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            ✔ Setujui
                        </button>
                    </form>

                    <form method="POST" action="/admin/payments/{{ $payment->id }}/reject">
                        @csrf
                        <button class="border border-red-500 text-red-500 px-4 py-2 rounded-lg text-sm hover:bg-red-50">
                            ✖ Tolak
                        </button>
                    </form>

                    @endif

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection
