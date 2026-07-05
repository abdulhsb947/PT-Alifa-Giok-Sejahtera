@extends('layouts.admin')

@section('content')

<div class="p-6 space-y-6">

    <div>
        <h1 class="text-3xl font-bold">
            Dashboard Admin
        </h1>

        <p class="text-gray-500">
            Ringkasan aktivitas sistem penyewaan
        </p>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">Total Pesanan</p>
            <h2 class="text-3xl font-bold">
                {{ $totalOrders }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">Rental Aktif</p>
            <h2 class="text-3xl font-bold text-blue-600">
                {{ $activeRentals }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">Menunggu Verifikasi</p>
            <h2 class="text-3xl font-bold text-yellow-600">
                {{ $pendingOrders }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">Pendapatan</p>
            <h2 class="text-3xl font-bold text-green-600">
                Rp {{ number_format($totalRevenue) }}
            </h2>
        </div>

    </div>

    {{-- OPERASIONAL --}}
    <div class="grid md:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">
                Pembayaran Menunggu
            </p>

            <h3 class="text-2xl font-bold text-yellow-600">
                {{ $pendingPayments }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">
                Denda Menunggu
            </p>

            <h3 class="text-2xl font-bold text-red-600">
                {{ $pendingPenalty }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">
                Perawatan
            </p>

            <h3 class="text-2xl font-bold text-orange-600">
                {{ $maintenanceCount }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500">
                Produk Hilang
            </p>

            <h3 class="text-2xl font-bold text-orange-600">
                {{ $lostProductCount }}
            </h3>
        </div>

    </div>

    {{-- STOK --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold mb-4">
            Ringkasan Stok
        </h3>

        <div class="grid md:grid-cols-3 gap-4">

            <div>
                <p class="text-gray-500">Total</p>
                <p class="font-bold text-xl">
                    {{ $totalStock }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Tersedia</p>
                <p class="font-bold text-green-600 text-xl">
                    {{ $availableStock }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Disewa</p>
                <p class="font-bold text-blue-600 text-xl">
                    {{ $rentedStock }}
                </p>
            </div>

        

        </div>

    </div>

    {{-- ORDER & PAYMENT --}}
    <div class="grid lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl shadow p-5">

            <h3 class="font-bold mb-4">
                Pesanan Terbaru
            </h3>

            @foreach($latestOrders as $order)

            <div class="border-b py-3">

                <p class="font-semibold">
                    {{ $order->order_code }}
                </p>

                <p class="text-sm text-gray-500">
                    {{ $order->user->name ?? '-' }}
                </p>

            </div>

            @endforeach

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <h3 class="font-bold mb-4">
                Pembayaran Terbaru
            </h3>

            @foreach($latestPayments as $payment)

            <div class="border-b py-3">

                <p class="font-semibold">
                    PAY-{{ $payment->id }}
                </p>

                <p class="text-sm text-gray-500">
                    Rp {{ number_format($payment->amount) }}
                </p>

            </div>

            @endforeach

        </div>

    </div>

    {{-- RENTAL TERLAMBAT --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">

        <h3 class="font-bold text-red-600 mb-4">
            Rental Bermasalah / Denda
        </h3>

        @forelse($lateRentals as $rental)

        <div class="border-b py-3">

            <p class="font-semibold">
                {{ $rental->order->order_code }}
            </p>

            <p>
                Denda:
                Rp {{ number_format($rental->penalty->total_fee ?? 0) }}
            </p>

        </div>

        @empty

        <p class="text-gray-500">
            Tidak ada denda aktif
        </p>

        @endforelse

    </div>

</div>

@endsection