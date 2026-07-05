@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-center min-h-screen bg-gray-100">

<div class="bg-white w-full max-w-3xl rounded-xl shadow-lg p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Tambah Produk Baru</h2>
        <a href="/admin/products" class="text-gray-400 hover:text-gray-600">✖</a>
    </div>

    <!-- FORM -->
    <form method="POST" action="/admin/products" enctype="multipart/form-data">
        @csrf

        <!-- NAMA PRODUK -->
        <div class="mb-4">
            <label class="block mb-1">Nama Produk</label>
            <input type="text" name="name"
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <!-- KATEGORI -->
        <div class="mb-4">
            <label class="block mb-1">Kategori</label>
            <input type="text" name="category"
                placeholder="Contoh: Scaffolding Frame"
                class="w-full border rounded-lg px-4 py-2">
        </div>

        <!-- DESKRIPSI -->
        <div class="mb-4">
            <label class="block mb-1">Deskripsi</label>
            <textarea name="description"
                class="w-full border rounded-lg px-4 py-2 h-24"></textarea>
        </div>

        <!-- GAMBAR -->
        <div class="mb-4">
            <label class="block mb-1">Gambar Produk</label>
            <input type="file" name="image"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- HARGA -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

            <div>
                <label>Harga / Bulan</label>
                <input type="number" name="price_per_month"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

        </div>

        <!-- STOK -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label>Total Stok</label>
                <input type="number" name="total_stock"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <div>
                <label>Stok Tersedia</label>
                <input type="number" name="available_stock"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3">

            <a href="/admin/products"
                class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                Batal
            </a>

            <button
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Simpan Produk
            </button>

        </div>

    </form>

</div>

</div>

@endsection