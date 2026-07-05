@extends('layouts.app')

@section('content')

@php
$isAuthenticated = false;
$user = ['name' => 'User', 'role' => 'customer'];

$orders = [];
$rentals = [];

$isCustomer = $isAuthenticated && $user['role'] === 'customer';

$activeOrders = 0;
$activeRentals = 0;
$pendingPayments = 0;
$needsApproval = 0;
$recentOrders = [];

$stats = [
    ['value' => '500+', 'label' => 'Projects Completed'],
    ['value' => '150+', 'label' => 'Happy Clients'],
    ['value' => '10K+', 'label' => 'Equipment Units'],
    ['value' => '15+', 'label' => 'Years Experience'],
];

$features = [
    ['title' => 'Fast Delivery', 'description' => 'Quick delivery and setup at your construction site with professional handling.'],
    ['title' => 'Safety Certified', 'description' => 'All equipment meets international safety standards and regularly inspected.'],
    ['title' => 'Flexible Rental', 'description' => 'Daily, weekly, or monthly rental options to fit your project timeline.'],
    ['title' => '24/7 Support', 'description' => 'Round-the-clock customer support for any inquiries or emergencies.'],
];

$benefits = [
    'Online ordering system',
    'Real-time stock availability',
    'Transparent pricing',
    'Site inspection service',
    'Professional maintenance',
    'Damage protection options',
];

$howItWorks = [
    ['step' => '1', 'title' => 'Create Order', 'desc' => 'Submit your project requirements online'],
    ['step' => '2', 'title' => 'Site Inspection', 'desc' => 'Our team visits and assesses your site'],
    ['step' => '3', 'title' => 'Agreement', 'desc' => 'Review and approve the rental terms'],
    ['step' => '4', 'title' => 'Payment', 'desc' => 'Upload payment proof after bank transfer'],
    ['step' => '5', 'title' => 'Delivery', 'desc' => 'Equipment delivered to your site'],
];
@endphp


<section class="relative gradient-hero min-h-[90vh] flex items-center overflow-hidden">

    <!-- BACKGROUND BLUR -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-300 rounded-full blur-3xl"></div>
    </div>

    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- LEFT -->
            <div class="text-center lg:text-left space-y-6 animate-fade-in">

                <div class="inline-flex items-center gap-2 bg-blue-500/20 text-white px-4 py-2 rounded-full text-sm font-medium">
                    <i data-lucide="star" class="w-4 h-4 fill-blue-300"></i>
                    Dipercaya oleh lebih dari 150 perusahaan konstruksi
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    Professional 
                    <span class="text-gradient">Scaffolding</span> 
                    Solusi Penyewaan
                </h1>

                <p class="text-lg text-white/70 max-w-xl mx-auto lg:mx-0">
                    Sederhanakan proyek konstruksi Anda dengan sistem manajemen penyewaan scaffolding kami yang lengkap. Pesan secara online, lacak penyewaan, dan kelola semuanya dari satu platform.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">

                    <a href="/register" class="bg-white text-blue-700 px-6 py-3 rounded-lg flex items-center gap-2">
                        Mulailah Proyek Anda
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>

                    <a href="/products" class="border border-white px-6 py-3 rounded-lg text-white">
                        Telusuri Peralatan
                    </a>

                </div>

                <!-- STATS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-8 border-t border-white/10">

                    <div>
                        <div class="text-2xl font-bold text-blue-300">500+</div>
                        <div class="text-sm text-white/60">Proyek yang Telah Selesai</div>
                    </div>

                    <div>
                        <div class="text-2xl font-bold text-blue-300">150+</div>
                        <div class="text-sm text-white/60">Klien yang Puas</div>
                    </div>

                    <div>
                        <div class="text-2xl font-bold text-blue-300">10K+</div>
                        <div class="text-sm text-white/60">Unit Peralatan</div>
                    </div>

                    <div>
                        <div class="text-2xl font-bold text-blue-300">15+</div>
                        <div class="text-sm text-white/60">Tahun Pengalaman</div>
                    </div>

                </div>

            </div>

            <!-- RIGHT CARD -->
            <div class="hidden lg:block relative">

                <div class="relative w-full aspect-square max-w-lg mx-auto">

                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/30 to-blue-300/10 rounded-3xl transform rotate-6"></div>

                    <div class="absolute inset-0 bg-white rounded-3xl shadow-2xl flex items-center justify-center">

                        <div class="text-center p-8">

                            <div class="w-32 h-32 mx-auto mb-6 rounded-2xl bg-blue-600 flex items-center justify-center">
                                <i data-lucide="truck" class="w-16 h-16 text-white"></i>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                Pesan scaffolding Secara Online
                            </h3>

                            <p class="text-gray-500 text-sm">
                                Manajemen penyewaan digital yang lengkap
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</section>

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

<!-- CTA -->
<section class="py-20 bg-blue-700 text-white text-center">

    <h2 class="text-3xl font-bold mb-4">
        Siap untuk Memulai?
    </h2>

    <p class="text-gray-200 mb-6">
        Bergabunglah dengan ratusan perusahaan konstruksi.
    </p>

    <a href="/register" class="bg-white text-blue-700 px-6 py-3 rounded flex items-center justify-center gap-2 w-fit mx-auto">
        Buat Akun Gratis
        <i data-lucide="arrow-right" class="w-5 h-5"></i>
    </a>

</section>

</div>

@endsection

