@extends('layouts.customer')

@section('content')

@php
$user = auth()->user();

$stats = [
    ['title' => 'Active Orders', 'value' => '3', 'icon' => 'shopping-cart'],
    ['title' => 'Active Rentals', 'value' => '2', 'icon' => 'truck'],
    ['title' => 'Pending Payments', 'value' => '1', 'icon' => 'credit-card'],
    ['title' => 'Total Transactions', 'value' => '12', 'icon' => 'clock'],
];

$recentOrders = [
    [
        'id' => 'ORD-2024-001',
        'project' => 'Tower A Construction',
        'product' => 'Frame Scaffolding',
        'status' => 'waiting_customer_approval',
        'date' => '2024-01-15',
    ],
    [
        'id' => 'ORD-2024-002',
        'project' => 'Mall Renovation',
        'product' => 'Ringlock System',
        'status' => 'active_rental',
        'date' => '2024-01-10',
    ],
    [
        'id' => 'ORD-2024-003',
        'project' => 'Office Building',
        'product' => 'Mobile Tower',
        'status' => 'waiting_payment',
        'date' => '2024-01-08',
    ],
];

$statusLabels = [
    'waiting_customer_approval' => 'Needs Your Approval',
    'waiting_payment' => 'Awaiting Payment',
    'active_rental' => 'Active Rental',
];
@endphp


<div class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-3xl font-bold">
            Dashboard
        </h1>

        <p class="text-gray-500">
            Selamat datang, {{ auth()->user()->name }}
        </p>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Total Pesanan
            </p>

            <h2 class="text-3xl font-bold">
                {{ $totalOrders }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Penyewaan Aktif
            </p>

            <h2 class="text-3xl font-bold text-blue-600">
                {{ $activeRentals }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">
                Total Pembayaran
            </p>

            <h2 class="text-3xl font-bold text-green-600">
                Rp {{ number_format($totalPayments) }}
            </h2>
        </div>

    </div>

    {{-- CONTENT --}}
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- PESANAN TERAKHIR --}}
        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="font-bold text-lg mb-4">
                Pesanan Terakhir
            </h3>

            @if($latestOrder)

            <div class="space-y-2">

                <p>
                    <span class="font-semibold">
                        Kode:
                    </span>
                    {{ $latestOrder->order_code }}
                </p>

                <p>
                    <span class="font-semibold">
                        Proyek:
                    </span>
                    {{ $latestOrder->project_name }}
                </p>

                <p>
                    <span class="font-semibold">
                        Status:
                    </span>

                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-600">
                        {{ ucfirst(str_replace('_',' ',$latestOrder->status)) }}
                    </span>
                </p>

            </div>

            @else

            <p class="text-gray-500">
                Belum ada pesanan
            </p>

            @endif

        </div>

        {{-- PEMBAYARAN TERAKHIR --}}
        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="font-bold text-lg mb-4">
                Pembayaran Terakhir
            </h3>

            @if($latestPayment)

            <div class="space-y-2">

                <p>
                    <span class="font-semibold">
                        ID:
                    </span>

                    PAY-{{ $latestPayment->id }}
                </p>

                <p>
                    <span class="font-semibold">
                        Tipe:
                    </span>

                    {{ strtoupper($latestPayment->payment_type) }}
                </p>

                <p>
                    <span class="font-semibold">
                        Nominal:
                    </span>

                    Rp {{ number_format($latestPayment->amount) }}
                </p>

                <p>
                    <span class="font-semibold">
                        Status:
                    </span>

                    <span class="px-2 py-1 rounded text-xs
                    @if($latestPayment->status == 'disetujui')
                    bg-green-100 text-green-600
                    @elseif($latestPayment->status == 'ditolak')
                    bg-red-100 text-red-600
                    @else
                    bg-yellow-100 text-yellow-600
                    @endif">
                        {{ ucfirst(str_replace('_',' ',$latestPayment->status)) }}
                    </span>
                </p>

            </div>

            @else

            <p class="text-gray-500">
                Belum ada pembayaran
            </p>

            @endif

        </div>

    </div>

    {{-- RENTAL --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold text-lg mb-4">
            Penyewaan Terakhir
        </h3>

        @if($latestRental)

        @php
            $order = $latestRental->order;

            $endDate = \Carbon\Carbon::parse($order->start_date)
                ->addMonths($order->duration);

            $isLate = now()->gt($endDate);
        @endphp

        <div class="grid md:grid-cols-4 gap-6">

            <div>
                <p class="text-gray-500 text-sm">
                    Kode Pesanan
                </p>

                <p class="font-semibold">
                    {{ $order->order_code }}
                </p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">
                    Mulai Sewa
                </p>

                <p class="font-semibold">
                    {{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }}
                </p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">
                    Berakhir
                </p>

                <p class="font-semibold">
                    {{ $endDate->format('d M Y') }}
                </p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">
                    Status
                </p>

                @if($isLate)

                <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-600">
                    Terlambat
                </span>

                @else

                <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-600">
                    Aktif
                </span>

                @endif

            </div>

        </div>

        @else

        <p class="text-gray-500">
            Belum ada penyewaan aktif
        </p>

        @endif

    </div>

    {{-- MENU CEPAT --}}
    <div class="grid md:grid-cols-4 gap-4">

        <a href="{{ route('customer.orders') }}"
           class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">

            <h4 class="font-semibold">
                📦 Pesanan
            </h4>

            <p class="text-sm text-gray-500">
                Kelola pesanan
            </p>

        </a>

        <a href="{{ route('customer.payment.list') }}"
           class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">

            <h4 class="font-semibold">
                💳 Pembayaran
            </h4>

            <p class="text-sm text-gray-500">
                Riwayat pembayaran
            </p>

        </a>

        <a href="{{ route('customer.rentals') }}"
           class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">

            <h4 class="font-semibold">
                🚚 Penyewaan
            </h4>

            <p class="text-sm text-gray-500">
                Lihat penyewaan
            </p>

        </a>

        <a href="{{ route('customer.settings') }}"
           class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">

            <h4 class="font-semibold">
                ⚙ Pengaturan
            </h4>

            <p class="text-sm text-gray-500">
                Profil akun
            </p>

        </a>

    </div>

</div>




<!-- FEATURES -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">
                Mengapa Memilih PT Alifa Giok Sejahtera?
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto">
               Kami menyediakan solusi penyewaan scaffolding yang lengkap, dengan mengutamakan keselamatan, keandalan, dan kepuasan pelanggan.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- ITEM -->
            <div class="p-6 bg-white rounded-xl border hover:shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="truck" class="w-6 h-6 text-blue-600"></i>
                </div>
                <h3 class="font-semibold">Pengiriman Cepat</h3>
                <p class="text-sm text-gray-500">
                    Pengiriman cepat dan pemasangan di lokasi proyek Anda dengan penanganan yang profesional.
                </p>
            </div>

            <div class="p-6 bg-white rounded-xl border hover:shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="shield" class="w-6 h-6 text-blue-600"></i>
                </div>
                <h3 class="font-semibold">Telah Disertifikasi Keamanannya</h3>
                <p class="text-sm text-gray-500">
                    Seluruh peralatan memenuhi standar keselamatan internasional dan diperiksa secara berkala.
                </p>
            </div>

            <div class="p-6 bg-white rounded-xl border hover:shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="clock" class="w-6 h-6 text-blue-600"></i>
                </div>
                <h3 class="font-semibold">Sewa Fleksibel</h3>
                <p class="text-sm text-gray-500">
                    Sewa bulanan yang sesuai dengan jadwal proyek Anda.
                </p>
            </div>

            <div class="p-6 bg-white rounded-xl border hover:shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="headphones" class="w-6 h-6 text-blue-600"></i>
                </div>
                <h3 class="font-semibold">24/7 Dukungan</h3>
                <p class="text-sm text-gray-500">
                    Layanan dukungan pelanggan 24 jam sehari untuk segala pertanyaan atau keadaan darurat.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- BENEFITS -->
<section class="py-20 bg-gray-100">
    <div class="container mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center">

        <div>
            <h2 class="text-3xl font-bold mb-6">
                Sistem Manajemen Penyewaan Terpadu
            </h2>

            <p class="text-gray-600 mb-8">
                Platform digital kami mempermudah seluruh proses penyewaan, mulai dari pemesanan hingga pengembalian, sehingga menjamin transparansi dan efisiensi di setiap tahapnya.
            </p>

            <div class="grid sm:grid-cols-2 gap-4">

                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="text-blue-600"></i>
                    Sistem pemesanan online
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="text-blue-600"></i>
                    Ketersediaan stok secara real-time
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="text-blue-600"></i>
                    Kebijakan harga yang transparan
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="text-blue-600"></i>
                    Layanan inspeksi lokasi
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="text-blue-600"></i>
                    Perawatan profesional
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="text-blue-600"></i>
                    Pilihan perlindungan terhadap kerusakan
                </div>

            </div>
        </div>

        <!-- HOW IT WORKS -->
        <div class="bg-white p-8 rounded-xl shadow">

            <h3 class="text-xl font-semibold mb-6">Cara Kerjanya</h3>

            @for($i=1; $i<=5; $i++)
            <div class="flex gap-4 mb-4">
                <div class="bg-blue-600 text-white w-10 h-10 flex items-center justify-center rounded-full font-bold">
                    {{ $i }}
                </div>
                <div>
                    <p class="font-semibold">
                        Step {{ $i }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Description of step {{ $i }}
                    </p>
                </div>
            </div>
            @endfor

        </div>

    </div>
</section>

@endsection