@extends('layouts.app')

@section('content')

@php
$milestones = [
  ['year' => '2009', 'title' => 'Perusahaan Didirikan', 'desc' => 'Dimulai dengan 100 unit scaffolding'],
  ['year' => '2012', 'title' => 'Ekspansi Regional', 'desc' => 'Expanded to 5 major cities'],
  ['year' => '2016', 'title' => '1000+ Proyek', 'desc' => 'Tonggak pencapaian proyek yang telah diselesaikan'],
  ['year' => '2020', 'title' => 'Platform Digital', 'desc' => 'Meluncurkan sistem penyewaan daring'],
  ['year' => '2024', 'title' => 'Pemimpin Pasar', 'desc' => 'Perusahaan Scaffolding terbesar di kawasan ini'],
];

$values = [
  ['icon' => 'target', 'title' => 'Kualitas Diutamakan', 'description' => 'Kami menjunjung tinggi standar tertinggi dalam semua peralatan dan layanan kami.'],
  ['icon' => 'eye', 'title' => 'Transparansi', 'description' => 'Harga yang transparan, komunikasi yang jujur, dan tanpa biaya tersembunyi.'],
  ['icon' => 'award', 'title' => 'Keamanan', 'description' => 'Semua peralatan memenuhi standar dan peraturan keselamatan internasional.'],
  ['icon' => 'users', 'title' => 'Berorientasi pada Pelanggan', 'description' => 'Kesuksesan Anda adalah prioritas kami. Kami menyesuaikan diri dengan kebutuhan proyek Anda.'],
];
@endphp

<div class="flex flex-col">

<!-- HERO -->
<section class="gradient-hero py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-6">
                Tentang PT Alifa Giok Sejahtera
            </h1>
            <p class="text-lg text-white/70">
                Selama lebih dari 15 tahun, kami telah menyediakan layanan penyewaan perancah yang andal bagi perusahaan konstruksi di seluruh wilayah ini.
            </p>
        </div>
    </div>
</section>

<!-- MISSION & VISION -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-8">

            <div class="bg-gray-50 p-8 rounded-2xl border">
                <div class="w-14 h-14 rounded-xl bg-blue-600 flex items-center justify-center mb-4">
                    <i data-lucide="target" class="w-7 h-7 text-white"></i>
                </div>
                <h2 class="text-2xl font-bold mb-4">Misi Kami</h2>
                <p class="text-gray-500">
                    Menyediakan layanan penyewaan scaffolding yang aman, andal, dan efisien, yang memungkinkan perusahaan konstruksi menyelesaikan proyek mereka tepat waktu dan sesuai anggaran
                </p>
            </div>

            <div class="bg-gray-50 p-8 rounded-2xl border">
                <div class="w-14 h-14 rounded-xl bg-blue-600 flex items-center justify-center mb-4">
                    <i data-lucide="eye" class="w-7 h-7 text-white"></i>
                </div>
                <h2 class="text-2xl font-bold mb-4">Visi Kami</h2>
                <p class="text-gray-500">
                    Menjadi perusahaan penyewaan perancah terkemuka di Asia Tenggara, yang dikenal karena komitmen kami terhadap keselamatan, inovasi, dan kepuasan pelanggan.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- VALUES -->
<section class="py-20 bg-gray-100">
    <div class="container mx-auto px-4">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Nilai-Nilai Utama Kami</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Prinsip-prinsip ini menjadi pedoman dalam setiap hal yang kami lakukan di PT Alifa Giok Sejahtera
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

            @foreach($values as $value)
            <div class="text-center p-6 bg-white rounded-2xl border hover:shadow-lg transition">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="{{ $value['icon'] }}" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">{{ $value['title'] }}</h3>
                <p class="text-gray-500 text-sm">{{ $value['description'] }}</p>
            </div>
            @endforeach

        </div>

    </div>
</section>

<!-- TIMELINE -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Nilai-Nilai Utama Kami</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Dari awal yang sederhana hingga menjadi pemimpin di industri ini.
            </p>
        </div>

        <div class="max-w-3xl mx-auto">
            <div class="relative">

                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>

                <div class="space-y-8">

                    @foreach($milestones as $m)
                    <div class="relative flex gap-6">
                        <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                            {{ substr($m['year'], 2) }}
                        </div>
                        <div class="bg-gray-50 p-6 rounded-xl border flex-1">
                            <div class="text-sm text-blue-600 font-semibold mb-1">{{ $m['year'] }}</div>
                            <h3 class="text-lg font-semibold mb-1">{{ $m['title'] }}</h3>
                            <p class="text-gray-500 text-sm">{{ $m['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>

            </div>
        </div>

    </div>
</section>

<!-- STATS -->
<section class="py-20 gradient-hero">
    <div class="container mx-auto px-4">

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center">

            <div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="building-2" class="text-white"></i>
                </div>
                <div class="text-4xl font-bold text-white">500+</div>
                <div class="text-white/70">Proyek</div>
            </div>

            <div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="text-white"></i>
                </div>
                <div class="text-4xl font-bold text-white">150+</div>
                <div class="text-white/70">Clients</div>
            </div>

            <div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="wrench" class="text-white"></i>
                </div>
                <div class="text-4xl font-bold text-white">10,000+</div>
                <div class="text-white/70">Peralatan</div>
            </div>

            <div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="award" class="text-white"></i>
                </div>
                <div class="text-4xl font-bold text-white">15+</div>
                <div class="text-white/70">Tahun</div>
            </div>

        </div>

    </div>
</section>

<!-- CTA -->
<section class="py-20 bg-white text-center">
    <h2 class="text-3xl font-bold mb-4">Siap Bekerja Sama dengan Kami?</h2>
    <p class="text-gray-500 mb-6">
        Bergabunglah dengan semakin banyak perusahaan yang mempercayakan kebutuhan perancah mereka kepada PT Alifa Giok Sejahtera.
    </p>

    <a href="/register" class="bg-blue-600 text-white px-6 py-3 rounded inline-flex items-center gap-2">
    Daftar Sekarang
        <i data-lucide="arrow-right"></i>
    </a>
</section>

</div>

@endsection