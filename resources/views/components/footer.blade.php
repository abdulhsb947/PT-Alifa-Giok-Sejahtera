<footer class="bg-gray-700 text-gray-200 mt-16">
    <div class="container mx-auto px-6 py-12">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <!-- BRAND -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="layers" class="text-white w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xl">
                        Scaffold<span class="text-blue-400">Pro</span>
                    </span>
                </div>

                <p class="text-sm text-gray-300">
                    Solusi penyewaan perancah profesional untuk perusahaan konstruksi.
                    Peralatan berkualitas, layanan yang andal.
                </p>
            </div>

            <!-- QUICK LINKS -->
            <div>
    <h4 class="font-semibold text-lg mb-4">Tautan Cepat</h4>

    <ul class="space-y-2 text-sm">

        @php
            $prefix = auth()->check() && auth()->user()->role == 'customer' ? '/customer' : '';
        @endphp

        <li>
            <a href="{{ $prefix }}/" class="hover:text-blue-400">Beranda</a>
        </li>

        <li>
            <a href="{{ $prefix }}/about" class="hover:text-blue-400">Tentang</a>
        </li>

        <li>
            <a href="{{ $prefix }}/products" class="hover:text-blue-400">Produk</a>
        </li>

        <li>
            <a href="{{ $prefix }}/contact" class="hover:text-blue-400">Kontak</a>
        </li>

    </ul>
</div>

            <!-- SERVICES -->
            <div>
                <h4 class="font-semibold text-lg mb-4">Layanan</h4>
                <ul class="space-y-2 text-sm text-gray-300">

                    <li>Penyewaan Scaffolding</li>
                    <li>Inspeksi Lokasi</li>
                    <li>Pemeliharaan Peralatan</li>
                    <li>Konsultasi Keselamatan</li>

                </ul>
            </div>

            <!-- CONTACT -->
            <div>
                <h4 class="font-semibold text-lg mb-4">Informasi Kontak</h4>

                <ul class="space-y-3 text-sm text-gray-300">

                    <li class="flex items-center gap-3">
                        <i data-lucide="map-pin" class="w-4 h-4 text-blue-400"></i>
                        <span>123 Industrial Avenue, Jakarta, Indonesia</span>
                    </li>

                    <li class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-4 h-4 text-blue-400"></i>
                        <span>+62 21 1234 5678</span>
                    </li>

                    <li class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-4 h-4 text-blue-400"></i>
                        <span>info@scaffoldpro.com</span>
                    </li>

                </ul>
            </div>

        </div>

        <!-- LINE -->
        <hr class="border-gray-500 my-8">

        <!-- COPYRIGHT -->
        <div class="text-center text-sm text-gray-400">
            © 2024 ScaffoldPro. All rights reserved.
        </div>

    </div>
</footer>