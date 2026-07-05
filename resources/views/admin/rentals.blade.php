@extends('layouts.admin')

@section('content')

<div>
    <h2 class="text-2xl font-bold">Manajemen Penyewaan</h2>
    <p class="text-gray-500 mb-6">Kelola penyewaan yang sedang berjalan, selesai, dan terlambat</p>

    {{-- ====================== --}}
    {{-- RINGKASAN --}}
    {{-- ====================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-blue-100 p-3 rounded-lg">🚚</div>
            <div>
                <h3 class="text-xl font-bold">{{ $sedangDisewa ?? 0  }}</h3>
                <p class="text-gray-500 text-sm">Sedang Disewa</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-green-100 p-3 rounded-lg">✅</div>
            <div>
                <h3 class="text-xl font-bold">{{ $selesai ?? 0 }}</h3>
                <p class="text-gray-500 text-sm">Selesai</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-red-100 p-3 rounded-lg">⚠️</div>
            <div>
                <h3 class="text-xl font-bold">{{ $terlambat ?? 0  }}</h3>
                <p class="text-gray-500 text-sm">Terlambat</p>
            </div>
        </div>

    </div>

    {{-- ====================== --}}
    {{-- PESANAN SIAP DIKIRIM --}}
    {{-- ====================== --}}
    <h3 class="text-lg font-bold mb-3">Pesanan Siap Dikirim</h3>

    @forelse($orders as $order)
    <div class="bg-white p-4 rounded-xl shadow mb-4 flex justify-between items-center">
        <div>
            <p class="font-semibold">Kode: {{ $order->order_code }}</p>
            <p class="text-sm text-gray-500">{{ $order->user->name }}</p>
        </div>

        <form action="{{ route('admin.rental.send', $order->id) }}" method="POST">
            @csrf
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                Kirim Barang
            </button>
        </form>
    </div>
    @empty
    <div class="text-gray-400 mb-4">Tidak ada pesanan yang siap dikirim</div>
    @endforelse

    {{-- ====================== --}}
    {{-- LIST PENYEWAAN --}}
    {{-- ====================== --}}
    @forelse($rentals as $rental)

@php
    $order = $rental->order;

    $endDate = \Carbon\Carbon::parse($order->start_date)
        ->addMonths($order->duration);

    $isLate = now()->gt($endDate);

    // 🔥 STATUS LABEL
    $statusLabel = match($rental->status) {
        'menunggu_konfirmasi_pengembalian' => 'Menunggu Konfirmasi',
        'menunggu_pelunasan' => 'Menunggu Pelunasan',
        'menunggu_pembayaran_denda' => 'Menunggu Pembayaran Denda',
        'selesai' => 'Selesai',
        default => ($isLate ? 'Terlambat' : 'Sedang Disewa')
    };

    // 🔥 STATUS COLOR
    $statusColor = match($rental->status) {
        'menunggu_konfirmasi_pengembalian' => 'bg-yellow-100 text-yellow-600',
        'menunggu_pelunasan' => 'bg-blue-100 text-blue-600',
        'menunggu_pembayaran_denda' => 'bg-red-100 text-red-600',
        'selesai' => 'bg-green-100 text-green-600',
        default => ($isLate ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600')
    };

    // 🔥 PAYMENT
    $lastPayment = $order->payments
        ->where('status', 'disetujui')
        ->sortByDesc('created_at')
        ->first();

    $isLunas = $lastPayment && $lastPayment->payment_type == 'full';

    $paymentLabel = $isLunas ? 'Lunas' : 'DP';
    $paymentColor = $isLunas ? 'text-green-600' : 'text-yellow-600';

    $agreement = $order->agreement;
@endphp

<div class="bg-white rounded-xl shadow p-5 mb-4 hover:shadow-lg transition
{{ $rental->status == 'menunggu_konfirmasi_pengembalian' ? 'border-2 border-yellow-400' : '' }}">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-lg">
            #{{ str_pad($rental->id, 3, '0', STR_PAD_LEFT) }}

            <span class="ml-2 px-2 py-1 text-xs rounded {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </h5>

        <!-- BUTTON -->
        <!-- BUTTON -->
<div>
    @if(in_array($rental->status, ['disewakan', 'menunggu_konfirmasi_pengembalian']))
        <a href="{{ route('admin.rental.return.form', $rental->id) }}"
           class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm">
            Proses Pengembalian
        </a>
    @endif
</div>
    </div>

    <!-- 🔥 DETAIL GRID (HORIZONTAL RAPI) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-sm">

        <div>
            <p class="text-gray-400">Kode</p>
            <p class="font-semibold">{{ $order->order_code }}</p>
        </div>

        <div>
            <p class="text-gray-400">Pelanggan</p>
            <p class="font-semibold">{{ $order->user->name }}</p>
        </div>

        <div>
            <p class="text-gray-400">Produk</p>
            @foreach($order->items as $item)
                <p class="font-semibold">
                    {{ $item->product->name }} ({{ $item->quantity }})
                </p>
            @endforeach
        </div>

        <div>
            <p class="text-gray-400">Total</p>
            <p class="font-semibold">
                {{ $order->items->sum('quantity') }} unit
            </p>
        </div>

        <div>
            <p class="text-gray-400">Pembayaran</p>
            <p class="font-semibold {{ $paymentColor }}">
                {{ $paymentLabel }}
            </p>
        </div>

        <div>
            <p class="text-gray-400">Perjanjian</p>
            @if($agreement && $agreement->file)
                <a href="{{ asset('storage/'.$agreement->file) }}"
                   target="_blank"
                   class="text-blue-600 underline">
                    Lihat
                </a>
            @else
                <p class="text-gray-400">-</p>
            @endif
        </div>

        <div>
            <p class="text-gray-400">Periode</p>
            <p class="font-semibold">
                {{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }}
                -
                {{ $endDate->format('d M Y') }}
            </p>
        </div>

        <div>
            <p class="text-gray-400">Kirim</p>
            <p class="font-semibold">
                {{ \Carbon\Carbon::parse($rental->tanggal_kirim)->format('d M Y') }}
            </p>
        </div>

    </div>

    <!-- 🔥 DENDA -->
    @if($rental->penalty)

@php
$penaltyPayment = $order->payments
    ->where('payment_type', 'penalty')
    ->sortByDesc('created_at')
    ->first();
@endphp

<div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">

    <h4 class="font-semibold text-red-600 mb-2">Denda</h4>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">

        <div>
            <p class="text-gray-400">Terlambat</p>
            <p class="font-semibold">{{ $rental->penalty->late_days }} hari</p>
        </div>

        <div>
            <p class="text-gray-400">Keterlambatan</p>
            <p class="font-semibold">
                Rp {{ number_format($rental->penalty->late_fee) }}
            </p>
        </div>

        <div>
    <p class="text-gray-400">Kerusakan</p>

    <p class="font-semibold text-orange-600">
        Rp {{
            number_format(
                $rental->returnItems->sum('repair_cost')
            )
        }}
    </p>
</div>

<div>
    <p class="text-gray-400">Kehilangan</p>

    <p class="font-semibold text-red-600">
        Rp {{
            number_format(
                $rental->returnItems->sum('lost_cost')
            )
        }}
    </p>
</div>

        <div>
            <p class="text-gray-400">Total</p>
            <p class="font-bold text-red-600">
                Rp {{ number_format($rental->penalty->total_fee) }}
            </p>
        </div>

    </div>

    {{-- 🔥 STATUS DENDA --}}
    <div class="mt-3">
        @if(!$penaltyPayment)
            <span class="bg-red-100 text-red-600 px-3 py-1 rounded text-sm">
                Belum Dibayar
            </span>

        @elseif($penaltyPayment->status == 'menunggu_verifikasi')
            <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded text-sm">
                Menunggu Verifikasi
            </span>

        @elseif($penaltyPayment->status == 'disetujui')
            <span class="bg-green-100 text-green-600 px-3 py-1 rounded text-sm">
                Sudah Dibayar
            </span>
        @endif
    </div>

    @if($rental->penalty->notes)
    <p class="text-xs text-gray-500 mt-2">
        {{ $rental->penalty->notes }}
    </p>
    @endif

</div>

@endif


</div>

@empty
<div class="bg-white p-6 rounded-xl shadow text-center text-gray-500">
    Belum ada data penyewaan
</div>
@endforelse

</div>

@endsection