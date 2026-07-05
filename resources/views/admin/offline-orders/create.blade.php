@extends('layouts.admin')

@section('content')

<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Pemesanan Offline</h1>
            <p class="text-sm text-gray-500">Buat pemesanan penyewaan scaffolding langsung dari admin.</p>
        </div>

        <a href="{{ route('offline-orders.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="rounded-lg bg-red-100 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('offline-orders.store') }}" method="POST">
        @csrf

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900">Data Penyewa</h2>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Customer</label>
                            <input type="text" name="customer_name" required
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">No HP</label>
                            <input type="text" name="phone"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900">Data Proyek</h2>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Proyek</label>
                            <input type="text" name="project_name" required
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal Mulai</label>
                            <input type="date" name="start_date" required
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Durasi Sewa</label>
                            <input type="number" name="duration" min="1" required
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Lokasi Proyek</label>
                            <textarea name="project_location" rows="3"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-600">Catatan</label>
                        <textarea name="notes" rows="3"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Pilih Produk</h2>
                            <p class="text-sm text-gray-500">Centang produk lalu atur jumlah yang disewa.</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach($products as $product)
                            <label class="block rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50/30">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <input type="checkbox"
                                            class="product-check mt-1 h-5 w-5 rounded border-gray-300 text-blue-600"
                                            name="products[]"
                                            value="{{ $product->id }}">

                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                            <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                                <span class="text-gray-500">Stok: {{ $product->available_stock }}</span>
                                                <span class="font-semibold text-blue-600">
                                                    Rp {{ number_format($product->price_per_month) }} / bulan
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:w-32">
                                        <label class="sr-only">Jumlah {{ $product->name }}</label>
                                        <input type="number"
                                            min="1"
                                            value="1"
                                            disabled
                                            name="quantities[{{ $product->id }}]"
                                            class="qty-input w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-right focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                            data-price="{{ $product->price_per_month }}">
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <div class="sticky top-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Item</span>
                            <span id="totalItems" class="font-semibold text-gray-900">0</span>
                        </div>

                        <div class="flex justify-between border-t border-gray-100 pt-3">
                            <span class="text-gray-500">Total Harga</span>
                            <span id="totalPrice" class="font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700">
                        Simpan Pemesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const checks = document.querySelectorAll('.product-check');
    const qtyInputs = document.querySelectorAll('.qty-input');

    checks.forEach((check, index) => {
        check.addEventListener('change', () => {
            qtyInputs[index].disabled = !check.checked;
            qtyInputs[index].value = 1;
            qtyInputs[index].classList.toggle('bg-gray-100', !check.checked);

            calculateTotal();
        });
    });

    function calculateTotal() {
        let total = 0;
        let items = 0;

        checks.forEach((check, index) => {
            if (check.checked) {
                const qty = parseInt(qtyInputs[index].value) || 0;
                const price = parseInt(qtyInputs[index].dataset.price) || 0;

                total += qty * price;
                items += qty;
            }
        });

        document.getElementById('totalItems').innerText = items;
        document.getElementById('totalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
</script>

@endsection
