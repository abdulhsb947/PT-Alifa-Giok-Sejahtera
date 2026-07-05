@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-center min-h-screen bg-gray-100">

<div class="bg-white w-full max-w-3xl rounded-xl shadow-lg p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Edit Produk</h2>
        <a href="/admin/products" class="text-gray-400 hover:text-gray-600">✖</a>
    </div>

    <!-- FORM -->
    <form method="POST" action="/admin/products/{{ $product->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- NAMA PRODUK -->
        <div class="mb-4">
            <label>Nama Produk</label>
            <input type="text" name="name" value="{{ $product->name }}"
                class="w-full border rounded-lg px-4 py-2">
        </div>

        <!-- KATEGORI -->
        <div class="mb-4">
            <label>Kategori</label>
            <input type="text" name="category" value="{{ $product->category }}"
                class="w-full border rounded-lg px-4 py-2">
        </div>

        <!-- DESKRIPSI -->
        <div class="mb-4">
            <label>Deskripsi</label>
            <textarea name="description"
                class="w-full border rounded-lg px-4 py-2 h-24">{{ $product->description }}</textarea>
        </div>

        <!-- GAMBAR -->
        <div class="mb-4">
            <label>Gambar Produk</label>

            <!-- Preview -->
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}"
                    class="h-24 mb-2 rounded">
            @endif

            <input type="file" name="image"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- HARGA -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

            <div>
                <label>Harga / Bulan</label>
                <input type="number" name="price_per_month" value="{{ $product->price_per_month }}"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

        </div>

        <!-- STOK -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label>Total Stok</label>
                <input type="number" name="total_stock" value="{{ $product->total_stock }}"
                    class="w-full border rounded-lg px-3 py-2">
                
                @error('total_stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label>Stok Tersedia</label>
                <input type="number" value="{{ $product->available_stock }}"
                    class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
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
                Perbarui Produk
            </button>

        </div>

    </form>

</div>

</div>

@endsection