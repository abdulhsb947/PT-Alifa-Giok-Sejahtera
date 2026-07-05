@extends('layouts.admin')

@section('content')

<div class="p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Manajemen Produk & Stok</h1>
            <p class="text-gray-500 text-sm">Kelola katalog produk dan ketersediaan stok</p>
        </div>

        <a href="/admin/products/create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <!-- PENCARIAN -->
    <div class="mb-6">
        <div class="relative max-w-md">
            <input type="text" id="searchInput"
                placeholder="Cari produk..."
                class="w-full border rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500">

            <i data-lucide="search"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
        </div>
    </div>

    <!-- GRID PRODUK -->
    <div id="productGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($products as $p)
        <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition product-card">

            <!-- GAMBAR -->
            <div class="h-32 bg-gray-100 rounded-lg flex items-center justify-center mb-4 overflow-hidden">
                @if($p->image)
                    <img src="{{ asset('storage/'.$p->image) }}"
                        class="w-full h-full object-cover">
                @else
                    <i data-lucide="box" class="w-10 h-10 text-gray-400"></i>
                @endif
            </div>

            <!-- KATEGORI -->
            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">
                {{ $p->category ?? 'Tanpa Kategori' }}
            </span>

            <!-- NAMA -->
            <h2 class="font-bold mt-2">{{ $p->name }}</h2>

            <!-- DESKRIPSI -->
            <p class="text-sm text-gray-500 mb-3">
                {{ $p->description }}
            </p>

            <!-- HARGA -->
            <div class="mb-3">
                <p class="text-sm text-gray-500">Harga / Bulan</p>
                <p class="text-blue-600 font-bold text-lg">
                    Rp {{ number_format($p->price_per_month) }}
                </p>
            </div>

            <!-- STOK -->
            <div class="grid grid-cols-2 gap-2 text-sm mb-4">

                <div class="bg-gray-100 p-2 rounded text-center">
                    <p class="font-bold text-green-600">{{ $p->available_stock }}</p>
                    <p class="text-xs text-gray-500">Tersedia</p>
                </div>

                <div class="bg-gray-100 p-2 rounded text-center">
                    <p class="font-bold text-yellow-600">{{ $p->rented_stock }}</p>
                    <p class="text-xs text-gray-500">Disewa</p>
                </div>

                <div class="bg-gray-100 p-2 rounded text-center">
                    <p class="font-bold text-red-600">{{ $p->maintenance_stock }}</p>
                    <p class="text-xs text-gray-500">Perbaikan</p>
                </div>

                <div class="bg-gray-100 p-2 rounded text-center">
                    <p class="font-bold text-blue-600">{{ $p->total_stock }}</p>
                    <p class="text-xs text-gray-500">Total</p>
                </div>

            </div>

            <!-- AKSI -->
            <div class="flex gap-2">

                <a href="/admin/products/{{ $p->id }}/edit"
                    class="flex-1 border border-blue-500 text-blue-600 py-2 rounded-lg text-center hover:bg-blue-50">
                    ✏️ Edit
                </a>

                <form id="delete-form-{{ $p->id }}"
      action="/admin/products/{{ $p->id }}"
      method="POST">
    @csrf
    @method('DELETE')

    <button
        type="button"
        onclick="confirmDelete({{ $p->id }})"
        class="bg-red-500 text-white px-3 rounded-lg hover:bg-red-600">
        🗑
    </button>
</form>

            </div>

        </div>
        @endforeach

    </div>

</div>

<!-- SCRIPT -->
<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    let cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        card.style.display = text.includes(value) ? '' : 'none';
    });
});

lucide.createIcons();
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endsection
