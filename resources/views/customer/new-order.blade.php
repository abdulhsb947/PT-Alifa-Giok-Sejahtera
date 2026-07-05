@extends('layouts.customer')

@section('content')

<div class="container mx-auto px-4 py-6 md:py-8 max-w-4xl">

    <!-- HEADER -->
    <div class="flex items-start md:items-center gap-3 mb-6">
        <a href="/customer/orders" class="text-gray-500 text-lg">←</a>

        <div>
            <h1 class="text-xl md:text-2xl font-bold">Buat Pesanan Baru</h1>
            <p class="text-gray-500 text-sm">
                Isi detail proyek untuk mengajukan penyewaan scaffolding
            </p>
        </div>
    </div>

    <!-- ERROR -->
    @if(session('error'))
    <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
        {{ session('error') }}
    </div>
    @endif

    <!-- STEPPER -->
    <div class="flex flex-wrap items-center gap-2 md:gap-0 justify-between mb-8 text-xs md:text-sm">

        <div class="flex items-center gap-2 text-blue-600">
            <div class="w-7 h-7 md:w-8 md:h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">
                1
            </div>
            <span class="font-medium hidden sm:inline">Detail Proyek</span>
        </div>

        <div class="flex-1 h-[2px] bg-gray-300 hidden md:block mx-2"></div>

        <div class="flex items-center gap-2 text-gray-400">
            <div class="w-7 h-7 md:w-8 md:h-8 bg-gray-300 rounded-full flex items-center justify-center">
                2
            </div>
            <span class="hidden sm:inline">Produk</span>
        </div>

        <div class="flex-1 h-[2px] bg-gray-300 hidden md:block mx-2"></div>

        <div class="flex items-center gap-2 text-gray-400">
            <div class="w-7 h-7 md:w-8 md:h-8 bg-gray-300 rounded-full flex items-center justify-center">
                3
            </div>
            <span class="hidden sm:inline">Dokumen</span>
        </div>

        <div class="flex-1 h-[2px] bg-gray-300 hidden md:block mx-2"></div>

        <div class="flex items-center gap-2 text-gray-400">
            <div class="w-7 h-7 md:w-8 md:h-8 bg-gray-300 rounded-full flex items-center justify-center">
                4
            </div>
            <span class="hidden sm:inline">Review</span>
        </div>

    </div>

    <!-- CARD -->
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">

        <h2 class="text-lg font-bold mb-1">Detail Proyek</h2>
        <p class="text-gray-500 text-sm mb-4">
            Informasikan detail proyek Anda
        </p>

        <form method="POST" action="/customer/orders/step2" class="space-y-4" id="projectForm">
            @csrf

            <!-- NAMA PROYEK -->
            <div>
                <label class="block mb-1">Nama Proyek *</label>
                <input type="text" name="project_name" required
                    value="{{ session('order.project_name') }}"
                    placeholder="Contoh: Pembangunan Tower A Tahap 2"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <!-- LOKASI -->
            <div>
                <label class="block mb-1">Lokasi Proyek *</label>
                <textarea name="project_location" required
                    placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Selatan"
                    class="w-full border rounded-lg px-3 py-2 h-24 focus:ring-2 focus:ring-blue-500 outline-none">{{ session('order.project_location') }}</textarea>
            </div>

            <!-- TELEPON -->
            <div>
    <label class="block mb-1">Nomor Telepon *</label>
    <input type="text" name="phone" required
        value="{{ old('phone', session('order.phone') ?? auth()->user()->phone) }}"
        class="w-full border rounded-lg px-3 py-2">
        
    <p class="text-xs text-gray-400 mt-1">
        Nomor otomatis dari akun, bisa diubah jika berbeda
    </p>
</div>

            <!-- BUTTON -->
            <div class="flex flex-col md:flex-row justify-between gap-3 mt-6">

                <a href="/customer/orders"
                    class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100 text-center">
                    ← Batal
                </a>

                <button
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lanjut →
                </button>

            </div>

        </form>

    </div>

</div>

<!-- VALIDASI JS -->
<script>
document.getElementById('projectForm').addEventListener('submit', function(e) {

    let name = document.querySelector('[name="project_name"]').value.trim();
    let location = document.querySelector('[name="project_location"]').value.trim();
    let phone = document.querySelector('[name="phone"]').value.trim();

    if (!name || !location || !phone) {
        e.preventDefault();
        alert('Semua field wajib diisi');
        return;
    }

    // Validasi nomor HP sederhana
    if (phone.length < 10) {
        e.preventDefault();
        alert('Nomor telepon tidak valid');
    }

});
</script>

@endsection