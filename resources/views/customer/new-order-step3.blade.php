@extends('layouts.customer')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-3xl">

    <!-- HEADER -->
    <div class="flex items-center gap-3 mb-6">
        <a href="/customer/orders/create" class="text-gray-500">←</a>

        <div>
            <h1 class="text-2xl font-bold">Buat Pesanan Baru</h1>
            <p class="text-gray-500 text-sm">
                Lengkapi data untuk mengajukan penyewaan scaffolding
            </p>
        </div>
    </div>

    <!-- STEPPER -->
    <div class="flex flex-wrap justify-between gap-3 mb-8 text-sm">

        <div class="flex items-center gap-2 text-blue-600">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">✓</div>
            Proyek
        </div>

        <div class="flex items-center gap-2 text-blue-600">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">✓</div>
            Produk
        </div>

        <div class="flex items-center gap-2 text-blue-600 font-bold">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">3</div>
            Dokumen
        </div>

        <div class="flex items-center gap-2 text-gray-400">
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">4</div>
            Review
        </div>

    </div>

    <!-- CARD -->
    <div class="bg-white p-6 rounded-xl shadow">

        <h2 class="text-lg font-bold mb-2">Upload Dokumen</h2>
        <p class="text-gray-500 text-sm mb-4">
            Unggah dokumen perusahaan yang diperlukan
        </p>

        <form method="POST" action="/customer/orders/step4" enctype="multipart/form-data">
            @csrf

            <!-- ERROR -->
            @if(session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                {{ session('error') }}
            </div>
            @endif

            <!-- JENIS DOKUMEN -->
            <div class="mb-4">
                <label class="block mb-2 font-medium">Jenis Dokumen *</label>

                <div class="flex flex-col md:flex-row gap-4">

                    <label class="flex items-center gap-2">
                        <input type="radio" name="document_type" value="npwp" required
                            {{ session('order.document_type') == 'npwp' ? 'checked' : '' }}>
                        NPWP (Nomor Pokok Wajib Pajak)
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="radio" name="document_type" value="spk"
                            {{ session('order.document_type') == 'spk' ? 'checked' : '' }}>
                        SPK (Surat Perintah Kerja)
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="radio" name="document_type" value="ktp"
                            {{ session('order.document_type') == 'ktp' ? 'checked' : '' }}>
                        KTP
                    </label>

                </div>
            </div>

            <!-- UPLOAD -->
            <div class="border-2 border-dashed rounded-lg p-6 text-center">

                <p class="text-gray-500 mb-2">
                    Unggah dokumen Anda
                </p>

                <p class="text-xs text-gray-400 mb-4">
                    Format: PDF, JPG, PNG (Maks. 10MB)
                </p>

                <input type="file" name="document" required
                    class="mx-auto block">

            </div>

            <!-- INFO EDIT -->
            @if(session('order.document_type'))
            <div class="bg-yellow-100 text-yellow-700 p-3 rounded mt-4">
                Anda sedang mengubah dokumen sebelumnya
            </div>
            @endif

            <!-- BUTTON -->
            <div class="flex flex-col md:flex-row justify-between gap-3 mt-6">

                <a href="/customer/orders/step2"
                    class="px-4 py-2 border rounded text-center">
                    ← Sebelumnya
                </a>

                <button class="px-6 py-2 bg-blue-600 text-white rounded">
                    Lanjut →
                </button>

            </div>

        </form>

    </div>

</div>

@endsection