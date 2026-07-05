@extends('layouts.app')

@section('content')

@php
$contactInfo = [
    [
        'icon' => 'map-pin',
        'title' => 'Alamat',
        'details' => ['JL. KURNIA 2 NO 40 LIMBUNGAN BARU', 'RUMBAI PEKANBARU RIAU. 28261', 'Indonesia']
    ],
    [
        'icon' => 'phone',
        'title' => 'Telepon',
        'details' => ['+62 21 1234 5678', '+62 812 3456 7890 (WhatsApp)']
    ],
    [
        'icon' => 'mail',
        'title' => 'Email',
        'details' => ['e-Mail alifa.giose@gmail.com']
    ],
    [
        'icon' => 'clock',
        'title' => 'Jam Operasional',
        'details' => ['Monday - Friday: 08:00 - 17:00', 'Saturday: 08:00 - 14:00']
    ],
];
@endphp

<div class="flex flex-col">

<!-- HERO -->
<section class="gradient-hero py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">Hubungi Kami</h1>
        <p class="text-white/70">Ada pertanyaan tentang layanan kami? Kami siap membantu. Silakan hubungi kami dan kami akan segera merespons.</p>
    </div>
</section>

<!-- CONTACT -->
<section class="py-16 bg-white">
<div class="container mx-auto px-4">

<div class="grid lg:grid-cols-3 gap-8">

<!-- LEFT INFO -->
<div class="space-y-6">

    <h2 class="text-2xl font-bold">Hubungi Kami</h2>
    <p class="text-gray-500">Baik Anda membutuhkan penawaran harga, memiliki pertanyaan tentang peralatan kami, atau ingin mendiskusikan solusi khusus, kami siap membantu.</p>

    <div class="space-y-6">

        @foreach($contactInfo as $item)
        <div class="flex gap-4">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                <i data-lucide="{{ $item['icon'] }}" class="text-white"></i>
            </div>

            <div>
                <h3 class="font-semibold mb-1">{{ $item['title'] }}</h3>

                @foreach($item['details'] as $detail)
                <p class="text-gray-500 text-sm">{{ $detail }}</p>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>

</div>

<!-- FORM -->
<div class="lg:col-span-2">

<div class="bg-white border rounded-2xl p-6 md:p-8 shadow">

    <h2 class="text-2xl font-bold mb-6">Kirim Pesan</h2>

    <form class="space-y-6">

        <div class="grid sm:grid-cols-2 gap-4">

            <div>
                <label class="block mb-1">Nama Lengkap *</label>
                <input type="text" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block mb-1">Email *</label>
                <input type="email" class="w-full border rounded px-3 py-2" required>
            </div>

        </div>

        <div class="grid sm:grid-cols-2 gap-4">

            <div>
                <label class="block mb-1">Telepon</label>
                <input type="text" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block mb-1">Perusahaan</label>
                <input type="text" class="w-full border rounded px-3 py-2">
            </div>

        </div>

        <div>
            <label class="block mb-1">Topik *</label>
            <input type="text" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block mb-1">Pesan *</label>
            <textarea rows="5" class="w-full border rounded px-3 py-2" required></textarea>
        </div>

        <button class="bg-blue-600 text-white px-6 py-3 rounded flex items-center gap-2">
            Kirim Pesan
            <i data-lucide="send"></i>
        </button>

    </form>

</div>

</div>

</div>

</div>
</section>

<!-- MAP -->
<section class="h-80 rounded-lg overflow-hidden">

    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.6222234245906!2d101.44521047568152!3d0.5680652635884034!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5ac971bca9db5%3A0x10cde9324d7b98b6!2sJl.%20Kurnia%20Raya%20No.2%2C%20Limbungan%20Baru%2C%20Rumbai%2C%20Kota%20Pekanbaru%2C%20Riau%2028266!5e0!3m2!1sid!2sid!4v1783019888446!5m2!1sid!2sid"
        class="w-full h-full"
        style="border:0;"
        allowfullscreen
        loading="lazy"
        referrerpolicy="strict-origin-when-cross-origin">
    </iframe>

</section>

</div>

@endsection