@extends('layouts.customer')

@section('content')

<div class="max-w-6xl mx-auto p-4 md:p-6">

    <h1 class="text-xl font-bold mb-6">Pembayaran</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- ========================= -->
    <!-- SUMMARY -->
    <!-- ========================= -->
    <div class="grid md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">⏳</div>
            <div>
                <p class="text-lg font-bold">{{ $pending ?? 0 }}</p>
                <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">✔</div>
            <div>
                <p class="text-lg font-bold">{{ $approved ?? 0 }}</p>
                <p class="text-sm text-gray-500">Disetujui</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-4">
            <div class="bg-red-100 text-red-600 p-3 rounded-full">✖</div>
            <div>
                <p class="text-lg font-bold">{{ $rejected ?? 0 }}</p>
                <p class="text-sm text-gray-500">Ditolak</p>
            </div>
        </div>

    </div>

    <!-- ========================= -->
    <!-- RIWAYAT PEMBAYARAN -->
    <!-- ========================= -->
    <div class="space-y-4 mb-8">

        <h2 class="font-bold text-lg">Riwayat Pembayaran</h2>

        @forelse($payments as $payment)

        <div class="bg-white p-5 rounded-xl shadow">

            <div class="grid md:grid-cols-3 gap-4 items-center">

                <!-- LEFT -->
                <div>
                    <p class="font-bold">
                        Order: {{ optional($payment->order)->order_code ?? '-' }}
                    </p>

                    <p class="text-sm text-gray-500">
                        Proyek: {{ optional($payment->order)->project_name ?? '-' }}
                    </p>

                    <p class="text-sm text-gray-500">
                        Telepon: {{ optional(optional($payment->order)->user)->phone ?? '-' }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        {{ $payment->created_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <!-- CENTER -->
                <div class="text-sm space-y-1">

                    <span class="px-2 py-1 text-xs rounded-full
    @if($payment->status == 'disetujui') bg-green-100 text-green-600
    @elseif($payment->status == 'ditolak') bg-red-100 text-red-600
    @elseif($payment->status == 'menunggu_verifikasi') bg-yellow-100 text-yellow-600
    @elseif($payment->status == 'menunggu_pembayaran') bg-blue-100 text-blue-600
    @endif
">

    @if($payment->status == 'disetujui')
        ✔ Disetujui
    @elseif($payment->status == 'ditolak')
        ✖ Ditolak
    @elseif($payment->status == 'menunggu_verifikasi')
        ⏳ Menunggu Verifikasi
        @elseif($payment->status == 'menunggu_pembayaran')
    💳 Menunggu Pembayaran
    @else
        {{ ucfirst(str_replace('_',' ', $payment->status)) }}
    @endif

</span>

                    <p>Jenis: <b>{{ strtoupper($payment->payment_type) }}</b></p>

                    @if($payment->proof)
                    <a href="{{ asset('storage/'.$payment->proof) }}"
                       target="_blank"
                       class="text-blue-600 underline text-xs">
                       Lihat Bukti
                    </a>
                    @else
                    <span class="text-xs text-gray-400">Belum ada bukti</span>
                    @endif

                    @if($payment->notes)
                        <p class="text-xs text-gray-500">
                            Catatan: {{ $payment->notes }}
                        </p>
                    @endif

                </div>

                <!-- RIGHT -->
                <div class="text-right">

                    @if($payment->status == 'ditolak')
                        <a href="/customer/payment/{{ $payment->order_id }}"
                           class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
                           ✏️ Edit Pembayaran
                        </a>
                    @endif

                </div>

            </div>

        </div>

        @empty
            <p class="text-gray-500">Belum ada pembayaran</p>
        @endforelse

    </div>

    <!-- ========================= -->
    <!-- AMBIL ORDER TERAKHIR -->
    <!-- ========================= -->
    <!-- ========================= -->
<!-- AMBIL DATA -->
<!-- ========================= -->
@php

    $order = $order ?? null;

    $lastPayment = $payments
        ->whereIn('status', [
            'menunggu_verifikasi',
            'disetujui',
            'ditolak'
        ])
        ->first();

@endphp


<!-- ========================= -->
<!-- INFO ORDER -->
<!-- ========================= -->
@if($order)

<div class="mb-4 bg-gray-50 p-4 rounded-lg text-sm">

    <p>
        <b>Kode Order:</b>
        {{ $order->order_code }}
    </p>

    <p>
        <b>Proyek:</b>
        {{ $order->project_name }}
    </p>

    <p>
        <b>Telepon:</b>
        {{ optional($order->user)->phone }}
    </p>

</div>

@endif


<!-- ========================= -->
<!-- PERJANJIAN FINAL -->
<!-- ========================= -->
@if(
    $order &&
    $order->agreement &&
    $order->agreement->final_file
)

<div class="bg-white p-6 rounded-xl shadow mb-6">

    <h3 class="font-bold mb-3">
        📄 Dokumen Perjanjian Final
    </h3>

    <iframe
        src="{{ asset('storage/'.$order->agreement->final_file) }}"
        class="w-full h-[500px] border rounded mb-4">
    </iframe>

    <a
        href="{{ asset('storage/'.$order->agreement->final_file) }}"
        target="_blank"
        class="text-blue-600 underline">

        👁 Lihat Dokumen

    </a>

</div>

@endif


<!-- ========================= -->
<!-- INFORMASI TAGIHAN -->
<!-- ========================= -->
@if($tagihan)

<div class="bg-blue-50 border border-blue-200 p-5 rounded-xl mb-6">

    <h3 class="font-bold text-blue-700 mb-4">

        💳 Informasi Tagihan

    </h3>

    <div class="space-y-3">

    <div class="flex justify-between">
        <span>Biaya Sewa Produk</span>

        <span class="font-semibold">
            Rp {{ number_format($tagihan->biaya_sewa ?? 0) }}
        </span>
    </div>

    <div class="flex justify-between">
        <span>Biaya Pemasangan</span>

        <span class="font-semibold">
            Rp {{ number_format($tagihan->biaya_pemasangan ?? 0) }}
        </span>
    </div>

    <div class="flex justify-between">
        <span>Biaya Pembongkaran</span>

        <span class="font-semibold">
            Rp {{ number_format($tagihan->biaya_pembongkaran ?? 0) }}
        </span>
    </div>

    <div class="flex justify-between">
        <span>Biaya Pengiriman</span>

        <span class="font-semibold">
            Rp {{ number_format($tagihan->biaya_pengiriman ?? 0) }}
        </span>
    </div>

    <div class="flex justify-between">
        <span>Biaya Lainnya</span>

        <span class="font-semibold">
            Rp {{ number_format($tagihan->biaya_lainnya ?? 0) }}
        </span>
    </div>

    <hr>

    <div class="flex justify-between text-lg">
        <span class="font-bold">
            Total Tagihan
        </span>

        <span class="font-bold text-blue-700">
            Rp {{ number_format($tagihan->total_tagihan ?? 0) }}
        </span>
    </div>

    <div class="flex justify-between">

        <span>Sudah Dibayar</span>

        <span class="font-semibold text-green-600">
            Rp {{ number_format($totalDibayar) }}
        </span>

    </div>

    <div class="flex justify-between">

        <span>Sisa Pembayaran</span>

        <span class="font-semibold text-red-600">
            Rp {{ number_format($sisaPembayaran) }}
        </span>

    </div>

    @if($pelunasanTagihan && $pelunasanTagihan->due_date)

    <div class="flex justify-between">

        <span>Tenggat Pelunasan</span>

        <span class="font-semibold text-yellow-700">
            {{ \Carbon\Carbon::parse($pelunasanTagihan->due_date)->format('d M Y') }}
        </span>

    </div>

    @endif

</div>

</div>

@endif


<!-- ========================= -->
<!-- BELUM ADA TAGIHAN -->
<!-- ========================= -->
@if(!$tagihan)

<div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl">

    <p class="text-yellow-700">

        ⏳ Admin belum membuat tagihan pembayaran.

    </p>

</div>

@endif


<!-- ========================= -->
<!-- FORM PEMBAYARAN -->
<!-- ========================= -->
@if(
    $order &&
    $tagihan &&
    $sisaPembayaran > 0 &&
    (!$hasPendingPayment || $editPayment)
)

<div class="bg-white p-6 rounded-xl shadow">

    <h2 class="font-bold mb-4">
        Kirim Pembayaran
    </h2>


    

    <form
        method="POST"
        action="{{ route('customer.payment.store', $order->id) }}"
        enctype="multipart/form-data">

    @csrf

    <input
        type="hidden"
        name="order_id"
        value="{{ $order->id }}">

    @if($editPayment)
        <input
            type="hidden"
            name="edit_payment_id"
            value="{{ $editPayment->id }}">
    @endif

    <div class="mb-3">
    <label>Jumlah Pembayaran</label>

    <input
    type="number"
    name="amount"
    max="{{ $sisaPembayaran }}"
    value="{{ $sisaPembayaran }}"
    class="w-full border p-2 rounded"
    required>
</div>

<div class="mb-3">
    <label>Jenis Pembayaran</label>

    <select
        name="payment_type"
        class="w-full border p-2 rounded">

        @if($totalDibayar <= 0)

            <option value="dp" @selected(old('payment_type', $editPayment->payment_type ?? '') == 'dp')>
                DP
            </option>

            <option value="lunas" @selected(old('payment_type', $editPayment->payment_type ?? '') == 'lunas')>
                Lunas
            </option>

        @else

            <option value="pelunasan" @selected(old('payment_type', $editPayment->payment_type ?? 'pelunasan') == 'pelunasan')>
                Pelunasan
            </option>

        @endif

    </select>
</div>

    <div class="mb-3">
        <label>Upload Bukti</label>

        <input
            type="file"
            name="proof"
            required>
    </div>

    <div class="mb-3">
        <label>Catatan</label>

        <textarea
            name="notes"
            class="w-full border p-2 rounded">{{ old('notes', $editPayment->notes ?? '') }}</textarea>
    </div>

    <button
        class="bg-blue-600 text-white px-4 py-2 rounded">

        Kirim Pembayaran

    </button>

</form>

</div>

@endif

</div>

@endsection
