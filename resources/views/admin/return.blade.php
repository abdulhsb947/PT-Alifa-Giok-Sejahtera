@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-4">Form Pengembalian Barang</h2>

    {{-- ====================== --}}
    {{-- INFO PESANAN --}}
    {{-- ====================== --}}
    <div class="mb-4 text-sm border-b pb-4">
        <p><span class="text-gray-400">Kode Pesanan:</span>
            <strong>{{ $rental->order->order_code }}</strong></p>

        <p><span class="text-gray-400">Pelanggan:</span>
            <strong>{{ $rental->order->user->name }}</strong></p>
    </div>

    {{-- ====================== --}}
    {{-- LOGIKA PEMBAYARAN --}}
    {{-- ====================== --}}
    @php
        $hasDP = $rental->order->payments
    ->where('payment_type','dp')
    ->where('status','disetujui')
    ->count() > 0;

$hasLunas = $rental->order->payments
    ->where('payment_type','lunas')
    ->where('status','disetujui')
    ->count() > 0;

// 🔥 FINAL LOGIC
$hasPelunasan = $rental->order->payments
    ->where('payment_type','pelunasan')
    ->where('status','disetujui')
    ->count() > 0;

$needsPelunasan =
    $hasDP &&
    !$hasLunas &&
    !$hasPelunasan;

$needsPenalty = ($lateDays > 0) || ($rental->penalty && $rental->penalty->total_fee > 0);

        // 🔥 TOTAL DIBAYAR (INFO SAJA)
        $totalPaid = $rental->order->payments
            ->where('status','disetujui')
            ->sum('amount');
    @endphp

    {{-- ====================== --}}
    {{-- INFO KETERLAMBATAN --}}
    {{-- ====================== --}}
    @if($lateDays > 0)
        <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
            Terlambat <strong>{{ $lateDays }} hari</strong><br>
            Denda Keterlambatan:
            <strong>Rp {{ number_format($lateFee) }}</strong>
        </div>
    @else
        <div class="bg-green-100 text-green-600 p-3 rounded mb-4 text-sm">
            Pengembalian tepat waktu (tidak ada denda keterlambatan)
        </div>
    @endif

    {{-- ====================== --}}
    {{-- STATUS PEMBAYARAN --}}
    {{-- ====================== --}}
    <div class="mb-4 text-sm">
        <p class="text-gray-400">Status Pembayaran:</p>

        <p class="font-semibold {{ $needsPelunasan ? 'text-yellow-600' : 'text-green-600' }}">
            {{ $needsPelunasan ? 'DP (Belum Lunas)' : 'Lunas' }}
        </p>
    </div>

    {{-- ====================== --}}
    {{-- TOTAL DIBAYAR --}}
    {{-- ====================== --}}
    <div class="bg-gray-50 p-3 rounded mb-4 text-sm">
        <p>
            <span class="text-gray-400">Total Sudah Dibayar:</span><br>
            <strong class="text-blue-600">
                Rp {{ number_format($totalPaid) }}
            </strong>
        </p>
    </div>

    {{-- ====================== --}}
    {{-- RIWAYAT PEMBAYARAN --}}
    {{-- ====================== --}}
    <div class="mb-4">
        <p class="font-semibold text-sm mb-2">Riwayat Pembayaran:</p>

        <div class="border rounded text-sm">
            @forelse($rental->order->payments as $pay)
                <div class="p-2 border-b flex justify-between">
                    <div>
                        <span class="font-medium">
                            {{ strtoupper($pay->payment_type) }}
                        </span>
                        <br>
                        <span class="text-gray-500 text-xs">
                            {{ $pay->created_at->format('d M Y') }}
                        </span>
                    </div>

                    <div class="text-right">
                        <span class="block font-semibold">
                            Rp {{ number_format($pay->amount) }}
                        </span>

                        <span class="text-xs
                            {{ $pay->status == 'disetujui' ? 'text-green-600' : '' }}
                            {{ $pay->status == 'ditolak' ? 'text-red-600' : '' }}
                            {{ $pay->status == 'menunggu_verifikasi' ? 'text-yellow-600' : '' }}">
                            {{ $pay->status }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-2 text-gray-400">Belum ada pembayaran</div>
            @endforelse
        </div>
    </div>

    {{-- ====================== --}}
    {{-- ALERT --}}
    {{-- ====================== --}}
    @if($needsPelunasan)
    <div class="bg-yellow-100 text-yellow-700 p-3 rounded mb-4 text-sm">
        ⚠️ Pembayaran masih DP — Admin harus membuat pelunasan
    </div>
    @endif

    @if($needsPenalty)
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
        ⚠️ Ada denda — Admin harus membuat tagihan denda
    </div>
    @endif

    {{-- ====================== --}}
    {{-- TOMBOL --}}
    {{-- ====================== --}}
    <div class="mb-4 flex gap-2">

        @if($needsPenalty)

<div class="bg-white rounded-xl shadow p-6 mb-4">

    <div class="flex items-center justify-between mb-5">

        <div>
            <h5 class="font-bold text-xl">
                ⚠️ Tagihan Denda
            </h5>

            <p class="text-sm text-gray-500">
                Denda harus diselesaikan sebelum penyewaan dinyatakan selesai
            </p>
        </div>

        <span class="px-4 py-2 rounded-full bg-red-100 text-red-600 text-sm font-semibold">
            Menunggu Pembayaran
        </span>

    </div>

    <div class="grid md:grid-cols-4 gap-4">

        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-500 text-sm">
                Keterlambatan
            </p>

            <p class="text-2xl font-bold">
                {{ $lateDays }}
            </p>

            <p class="text-sm text-gray-500">
                Hari
            </p>
        </div>

        <div class="bg-red-50 rounded-lg p-4">
            <p class="text-gray-500 text-sm">
                Denda Keterlambatan
            </p>

            <p class="text-xl font-bold text-red-600">
                Rp {{ number_format($lateFee) }}
            </p>
        </div>

        <div class="bg-orange-50 rounded-lg p-4">
            <p class="text-gray-500 text-sm">
                Denda Kerusakan
            </p>

            <p class="text-xl font-bold text-orange-600">
                Rp {{ number_format($rental->penalty->damage_fee ?? 0) }}
            </p>
        </div>

        <div class="bg-red-100 rounded-lg p-4 border border-red-200">
            <p class="text-gray-700 text-sm">
                Total Denda
            </p>

            <p class="text-2xl font-bold text-red-700">
                Rp {{ number_format($rental->penalty->total_fee ?? 0) }}
            </p>
        </div>

    </div>

</div>

@endif

    </div>

    {{-- ====================== --}}
    {{-- FORM --}}
    {{-- ====================== --}}
    <form method="POST" action="{{ route('admin.rental.return', $rental->id) }}">
    @csrf


    {{-- DETAIL KERUSAKAN / KEHILANGAN --}}
    <h3 class="font-bold text-red-600 mb-3">
        Detail Kerusakan / Kehilangan
    </h3>

    @foreach($rental->order->items as $item)

    <div class="border rounded-lg p-4 mb-4">

        <h4 class="font-semibold">
            {{ $item->product->name }}
        </h4>
        <p class="text-sm text-gray-500">
    Jumlah disewa:
    {{ $item->quantity }} unit
</p>

        <div class="grid md:grid-cols-2 gap-4 mt-3">

            <div>
                <label>Barang Rusak</label>
                <input
    type="number"
    min="0"
    max="{{ $item->quantity }}"
    name="damaged_qty[{{ $item->product_id }}]"
    value="0"
    class="border p-2 w-full rounded">
            </div>

            <div>
                <label>Barang Hilang</label>
                <input
    type="number"
    min="0"
    max="{{ $item->quantity }}"
    name="lost_qty[{{ $item->product_id }}]"
    value="0"
    class="border p-2 w-full rounded">
            </div>

            <div>
                <label>Biaya Perbaikan</label>
                <input
                    type="number"
                    name="repair_cost[{{ $item->product_id }}]"
                    value="0"
                    class="border p-2 w-full rounded">
            </div>

            <div>
                <label>Biaya Kehilangan</label>
                <input
                    type="number"
                    name="lost_cost[{{ $item->product_id }}]"
                    value="0"
                    class="border p-2 w-full rounded">
            </div>

        </div>

        <div class="mt-3">
            <label>Keterangan</label>

            <textarea
                name="item_notes[{{ $item->product_id }}]"
                class="border p-2 w-full rounded"
                rows="2"
                placeholder="Contoh: Frame bengkok, clamp hilang, pipa penyok"></textarea>
        </div>

    </div>

    @endforeach


        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded">
                Simpan Pengembalian
            </button>
        </div>

    </form>

</div>

@endsection