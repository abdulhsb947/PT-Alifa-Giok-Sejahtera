@extends('layouts.customer')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-6xl">

    <h1 class="text-xl md:text-2xl font-bold mb-6">Pilih Produk</h1>

    @if(session('error'))
    <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ url('/customer/orders/step3') }}" id="orderForm">
        @csrf

        <!-- GRID PRODUK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($products as $p)
            <div class="border rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col">

                <!-- GAMBAR -->
                <img src="{{ asset('storage/'.$p->image) }}"
                    class="w-full h-40 object-cover rounded mb-3">

                <!-- NAMA -->
                <h3 class="font-bold text-lg">{{ $p->name }}</h3>

                <!-- DESKRIPSI -->
                <p class="text-sm text-gray-500 mb-2 line-clamp-2">
                    {{ $p->description }}
                </p>

                <!-- HARGA -->
                <p class="text-blue-600 font-bold">
                    Rp {{ number_format($p->price_per_month) }} / bulan
                </p>

                <!-- STOK -->
                <p class="text-sm text-gray-500 mb-2">
                    Stok Tersedia:
                    <span class="font-semibold">{{ $p->available_stock }}</span>
                </p>

                <!-- PILIH -->
                <label class="flex items-center gap-2 mb-2">
                    <input type="checkbox"
                        class="product-check"
                        data-price="{{ $p->price_per_month }}"
                        data-stock="{{ $p->available_stock }}"
                        value="{{ $p->id }}"
                        name="products[]"
                        {{ in_array($p->id, session('order.products', [])) ? 'checked' : '' }}>
                    Pilih Produk
                </label>

                <!-- JUMLAH -->
                <input type="number"
                    name="quantities[{{ $p->id }}]"
                    value="{{ session('order.quantities')[$p->id] ?? 1 }}"
                    class="border p-2 rounded qty-input mt-auto"
                    min="1"
                    {{ in_array($p->id, session('order.products', [])) ? '' : 'disabled' }}>

            </div>
            @endforeach

        </div>

        <!-- TANGGAL + DURASI -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block mb-1">Tanggal Mulai *</label>
                <input type="date" name="start_date" required
                    value="{{ session('order.start_date') }}"
                    class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block mb-1">Durasi (Bulan) *</label>
                <input type="number" name="duration" min="1" required
                    value="{{ session('order.duration') ?? 1 }}"
                    class="w-full border p-2 rounded">
            </div>

        </div>

        <!-- CATATAN -->
        <div class="mt-6">
            <label class="block mb-1">Catatan Tambahan (Opsional)</label>
            <textarea name="notes"
                class="w-full border p-3 rounded"
                placeholder="Tulis permintaan tambahan...">{{ session('order.notes') }}</textarea>
        </div>

        <!-- TOTAL -->
        <div class="mt-6 p-4 bg-gray-100 rounded">
            <h2 class="font-bold">Total Harga</h2>
            <p id="total-price" class="text-blue-600 text-xl font-bold">
                Rp 0
            </p>
        </div>

        <!-- BUTTON -->
        <div class="flex flex-col md:flex-row justify-between gap-3 mt-6">

            <a href="/customer/orders/create"
                class="px-4 py-2 border rounded text-center">
                ← Sebelumnya
            </a>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">
                Lanjut →
            </button>

        </div>

    </form>

</div>

<!-- SCRIPT -->
<script>

// HITUNG TOTAL
function hitungTotal() {
    let total = 0;

    document.querySelectorAll('.product-check').forEach(cb => {
        if (cb.checked) {

            let harga = parseInt(cb.dataset.price);
            let qtyInput = document.querySelector(`[name="quantities[${cb.value}]"]`);
            let qty = parseInt(qtyInput.value) || 0;
            let durasi = parseInt(document.querySelector('[name="duration"]').value) || 1;

            total += harga * qty * durasi;
        }
    });

    document.getElementById('total-price').innerText =
        'Rp ' + total.toLocaleString('id-ID');
}

// EVENT
document.addEventListener('input', hitungTotal);
document.addEventListener('change', hitungTotal);

// VALIDASI SUBMIT
document.getElementById('orderForm').addEventListener('submit', function(e) {

    console.log('SUBMIT JALAN');

    let checked = document.querySelectorAll('.product-check:checked');

    if (checked.length === 0) {
        alert('Pilih minimal 1 produk');
        e.preventDefault();
        return;
    }

    let valid = true;

    checked.forEach(cb => {
        let qtyInput = document.querySelector(`[name="quantities[${cb.value}]"]`);
        let qty = parseInt(qtyInput.value);

        if (!qty || qty <= 0) {
            valid = false;
        }

        if (qty > parseInt(cb.dataset.stock)) {
            valid = false;
            alert('Jumlah melebihi stok!');
        }
    });

    if (!valid) {
        alert('Isi jumlah produk dengan benar');
        e.preventDefault();
        return;
    }

});

// AKTIFKAN QTY
document.querySelectorAll('.product-check').forEach(cb => {

    cb.addEventListener('change', function() {

        let qtyInput = document.querySelector(`[name="quantities[${this.value}]"]`);

        if (this.checked) {
            qtyInput.disabled = false;
            if (!qtyInput.value) qtyInput.value = 1;
        } else {
            qtyInput.disabled = true;
            qtyInput.value = '';
        }

        hitungTotal();
    });

});

// HITUNG AWAL
hitungTotal();

</script>

@endsection