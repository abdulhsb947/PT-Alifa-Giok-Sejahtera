@extends('layouts.app')

@section('content')

@php
$categories = ['Semua', 'Frame Scaffolding', 'Ringlock', 'Cuplock', 'Pipa'];

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}
@endphp

<div class="flex flex-col">

    <!-- HERO -->
    <section class="gradient-hero py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Produk Kami</h1>
            <p class="text-white/70">
                Lihat koleksi lengkap peralatan scaffolding kami. Semua produk telah bersertifikasi dan terawat dengan baik.
            </p>
        </div>
    </section>

    <!-- FILTER -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">

            <div class="flex flex-col md:flex-row gap-4 mb-8">

                <!-- SEARCH -->
                <div class="relative flex-1 max-w-md">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Cari produk..."
                        class="pl-10 w-full border rounded px-3 py-2">
                </div>

                <!-- CATEGORY -->
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                    <button onclick="filterCategory('{{ $cat }}')"
                        class="px-3 py-1 border rounded text-sm hover:bg-blue-100">
                        {{ $cat }}
                    </button>
                    @endforeach
                </div>

            </div>

            <p class="text-gray-500 mb-6">
                Menampilkan {{ count($products) }} produk
            </p>

            <!-- GRID -->
            <div id="productGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($products as $product)
                <div class="bg-white rounded-2xl border overflow-hidden hover:shadow-lg transition product-card">

                    <!-- IMAGE -->
                    <div class="relative h-48 bg-gray-100 flex items-center justify-center overflow-hidden">

                        @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}"
                            class="w-full h-full object-cover">
                        @else
                        <i data-lucide="package" class="w-16 h-16 text-gray-300"></i>
                        @endif

                        <!-- STOCK -->
                        <span class="absolute top-3 right-3 text-xs px-2 py-1 rounded 
                        {{ $product->available_stock > 20 ? 'bg-blue-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $product->available_stock }} tersedia
                        </span>
                    </div>

                    <div class="p-5">

                        <!-- CATEGORY -->
                        <div class="text-xs text-blue-600 mb-1 category">
                            {{ $product->category }}
                        </div>

                        <!-- NAME -->
                        <h3 class="text-lg font-semibold mb-2">
                            {{ $product->name }}
                        </h3>

                        <!-- DESC -->
                        <p class="text-gray-500 text-sm mb-4">
                            {{ $product->description }}
                        </p>

                        <!-- PRICE -->
                        <div class="space-y-1 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span>Harga / Bulan</span>
                                <span class="font-semibold text-blue-600">
                                    {{ formatPrice($product->price_per_month) }}
                                </span>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <a href="/login"
                            class="w-full bg-blue-600 text-white py-2 rounded flex items-center justify-center gap-2 hover:bg-blue-700">
                            <i data-lucide="shopping-cart"></i>
                            Login untuk Memesan
                        </a>

                    </div>

                </div>
                @endforeach

            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gray-100 text-center">
        <h2 class="text-2xl font-bold mb-4">Butuh Solusi Khusus?</h2>
        <p class="text-gray-500 mb-6">
            Hubungi kami untuk kebutuhan scaffolding khusus dan penawaran terbaik.
        </p>

        <a href="/contact" 
           class="bg-blue-600 text-white px-6 py-3 rounded inline-flex items-center gap-2 hover:bg-blue-700">
            Hubungi Kami
            <i data-lucide="arrow-right"></i>
        </a>
    </section>

</div>

<!-- SCRIPT -->
<script>

// 🔍 SEARCH
document.getElementById('searchInput').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        card.style.display = text.includes(value) ? '' : 'none';
    });
});

// 🏷 FILTER KATEGORI
function filterCategory(category) {
    let cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        let cat = card.querySelector('.category').innerText;

        if (category === 'Semua' || cat === category) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ICON
lucide.createIcons();

</script>

@endsection