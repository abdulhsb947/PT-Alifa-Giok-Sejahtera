@extends('layouts.customer')

@section('content')

    <div class="max-w-6xl mx-auto p-6">

        <!-- HEADER -->
        <h2 class="text-2xl font-bold">Penyewaan Aktif</h2>
        <p class="text-gray-500 mb-6">Lihat penyewaan scaffolding Anda saat ini</p>

        {{-- ====================== --}}
        {{-- SUMMARY --}}
        {{-- ====================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

            <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
                <div class="bg-blue-100 p-3 rounded-lg">🚚</div>
                <div>
                    <h3 class="text-xl font-bold">{{ $sedangDisewa }}</h3>
                    <p class="text-gray-500 text-sm">Sedang Disewa</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
                <div class="bg-green-100 p-3 rounded-lg">✅</div>
                <div>
                    <h3 class="text-xl font-bold">{{ $selesai }}</h3>
                    <p class="text-gray-500 text-sm">Selesai</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
                <div class="bg-red-100 p-3 rounded-lg">⚠️</div>
                <div>
                    <h3 class="text-xl font-bold">{{ $terlambat }}</h3>
                    <p class="text-gray-500 text-sm">Terlambat</p>
                </div>
            </div>

        </div>

        {{-- ====================== --}}
        {{-- LIST RENTAL --}}
        {{-- ====================== --}}
        @forelse($dataSewa as $rental)
            @php
                $order = $rental->order;

                $endDate = \Carbon\Carbon::parse($order->start_date)->addMonths($order->duration);

                $isLate = now()->gt($endDate);

                $label =
                    $rental->status == 'selesai'
                        ? 'Selesai'
                        : ($rental->status == 'menunggu_konfirmasi_pengembalian'
                            ? 'Menunggu Konfirmasi'
                            : ($isLate
                                ? 'Terlambat'
                                : 'Sedang Berjalan'));

                $color =
                    $rental->status == 'selesai'
                        ? 'bg-green-100 text-green-600'
                        : ($isLate
                            ? 'bg-red-100 text-red-600'
                            : 'bg-blue-100 text-blue-600');

                // 🔥 STATUS PEMBAYARAN (AMBIL DARI PAYMENT)
                $lastPayment = $order->payments->where('status', 'disetujui')->sortByDesc('created_at')->first();

                $isLunas = $lastPayment && in_array($lastPayment->payment_type, ['lunas', 'pelunasan']);

                $paymentLabel = $isLunas ? 'Lunas' : 'DP';

                $paymentColor = $isLunas ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600';
            @endphp

            <div
                class="bg-white rounded-xl shadow p-5 mb-4 
        {{ $rental->penalty ? 'border border-yellow-300' : '' }}">

                <!-- HEADER -->
                <h5 class="font-bold text-lg">
                    {{ str_pad($rental->id, 3, '0', STR_PAD_LEFT) }}

                    <span class="ml-2 px-2 py-1 text-xs rounded {{ $color }}">
                        {{ $label }}
                    </span>

                    {{-- 🔥 STATUS PEMBAYARAN --}}
                    <span class="px-2 py-1 text-xs rounded {{ $paymentColor }}">
                        {{ $paymentLabel }}
                    </span>

                    @if ($rental->penalty)
                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-600 rounded">
                            Ada Denda
                        </span>
                    @endif
                </h5>

                <!-- DETAIL -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-4 text-sm">

                    <div>
                        <p class="text-gray-400">Kode Pesanan</p>
                        <p class="font-semibold">{{ $order->order_code }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400">Produk</p>
                        @foreach ($order->items as $item)
                            <p class="font-semibold">
                                {{ $item->product->name }} ({{ $item->quantity }})
                            </p>
                        @endforeach
                    </div>

                    <div>
                        <p class="text-gray-400">Total Unit</p>
                        <p class="font-semibold">
                            {{ $order->items->sum('quantity') }} unit
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Periode Sewa</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($order->start_date)->translatedFormat('d M Y') }}
                            —
                            {{ $endDate->translatedFormat('d M Y') }}
                        </p>
                    </div>

                </div>

                @if ($rental->penalty)
                    @php
                        $penaltyPayment = $order->payments
                            ->where('payment_type', 'penalty')
                            ->sortByDesc('created_at')
                            ->first();

                        $repairCost = $rental->returnItems->sum('repair_cost');
                        $lostCost = $rental->returnItems->sum('lost_cost');
                    @endphp

                    <div class="mt-6 border-t pt-4">

                        <h5 class="font-bold text-red-600 mb-4">
                            ⚠️ Detail Denda
                        </h5>

                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">

                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">
                                    Hari Terlambat
                                </p>

                                <p class="font-bold text-lg">
                                    {{ $rental->penalty->late_days }}
                                </p>
                            </div>

                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">
                                    Denda Keterlambatan
                                </p>

                                <p class="font-bold text-red-600">
                                    Rp {{ number_format($rental->penalty->late_fee) }}
                                </p>
                            </div>

                            <div class="bg-orange-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">
                                    Denda Kerusakan
                                </p>

                                <p class="font-bold text-orange-600">
                                    Rp {{ number_format($repairCost) }}
                                </p>
                            </div>

                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-500">
                                    Denda Kehilangan
                                </p>

                                <p class="font-bold text-red-700">
                                    Rp {{ number_format($lostCost) }}
                                </p>
                            </div>

                            <div class="bg-red-100 p-3 rounded-lg border border-red-200">
                                <p class="text-xs text-gray-600">
                                    Total Denda
                                </p>

                                <p class="font-bold text-xl text-red-700">
                                    Rp {{ number_format($rental->penalty->total_fee) }}
                                </p>
                            </div>

                        </div>

                        @if ($rental->penalty->notes)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                                <p class="text-sm text-yellow-800">
                                    {{ $rental->penalty->notes }}
                                </p>
                            </div>
                        @endif

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <div>
                                <p class="text-sm text-gray-500">
                                    Status Denda
                                </p>

                                @if (!$penaltyPayment || $penaltyPayment->status == 'menunggu_pembayaran')
                                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm">
                                        Menunggu Pembayaran
                                    </span>
                                @elseif($penaltyPayment->status == 'menunggu_verifikasi')
                                    <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-lg text-sm">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($penaltyPayment->status == 'disetujui')
                                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-lg text-sm">
                                        Denda Sudah Dibayar
                                    </span>
                                @endif
                            </div>

                            <div>

                                @if (!$penaltyPayment || $penaltyPayment->status == 'menunggu_pembayaran')
                                    <a href="{{ route('penalty.pay.form', $rental->penalty->id) }}"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                        Bayar Denda
                                    </a>
                                @endif

                            </div>

                        </div>

                    </div>
                @endif
            </div>


            {{-- ====================== --}}
            {{-- 🔥 PELUNASAN --}}
            {{-- ====================== --}}
            @if ($rental->status == 'menunggu_pelunasan')
                <div class="mt-4">
                    <span class="text-yellow-600 text-sm font-semibold block mb-2">
                        Menunggu Pelunasan
                    </span>

                    <a href="{{ route('payment.payRemaining', $rental->order->id) }}"
                        class="inline-block bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        Bayar Pelunasan
                    </a>
                </div>
            @endif


            {{-- ====================== --}}
            {{-- 🔥 STATUS DENDA --}}
            {{-- ====================== --}}
            @if ($rental->status == 'menunggu_pembayaran_denda')
                <div class="mt-4">
                    <span class="text-red-600 text-sm font-semibold">
                        Menunggu Pembayaran Denda
                    </span>
                </div>
            @endif


            {{-- ====================== --}}
            {{-- 🔥 SELESAI --}}
            {{-- ====================== --}}
            @if ($rental->status == 'selesai')
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-semibold">
                        Penyewaan Selesai
                    </span>
                </div>
            @endif


        @empty

            <div class="bg-white p-6 rounded-xl shadow text-center text-gray-500">
                Belum ada data penyewaan
            </div>
        @endforelse

    </div>

@endsection
