@extends('layouts.admin')

@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-4">
    Buat Tagihan Pembayaran
</h2>

    {{-- INFO ORDER --}}
    <div class="mb-4 text-sm border-b pb-4">
        <p><span class="text-gray-400">Kode Order:</span>
            <strong>{{ $order->order_code }}</strong></p>

        <p><span class="text-gray-400">Pelanggan:</span>
            <strong>{{ $order->user->name }}</strong></p>
    </div>

    @if($order->agreement && $order->agreement->final_file)

<div class="mb-6">

    <h3 class="font-semibold mb-2">
        📄 Perjanjian Final yang Sudah Ditandatangani
    </h3>

    <iframe
        src="{{ asset('storage/'.$order->agreement->final_file) }}"
        class="w-full h-[500px] border rounded">
    </iframe>

    <div class="mt-2">
        <a
            href="{{ asset('storage/'.$order->agreement->final_file) }}"
            target="_blank"
            class="text-blue-600 underline">

            Buka PDF di Tab Baru

        </a>
    </div>

</div>

@endif

@php
$totalSewa = 0;

foreach($order->items as $item) {
    $totalSewa +=
        $item->price *
        $item->quantity *
        $order->duration;
}
@endphp

    {{-- FORM --}}
    <form method="POST" action="{{ route('admin.payments.store') }}">
        @csrf

        <input type="hidden" name="order_id" value="{{ $order->id }}">

        {{-- TOTAL TAGIHAN --}}
<div class="mb-4">

    <div class="space-y-4">

    <div>
        <label class="block mb-1 font-medium">
            Biaya Sewa Produk
        </label>

        <input
    type="number"
    id="biaya_sewa"
    name="biaya_sewa"
    class="border p-2 w-full rounded bg-gray-100"
    value="{{ $totalSewa }}"
    readonly>
    </div>

    <div>
        <label class="block mb-1 font-medium">
            Biaya Pemasangan
        </label>

        <input
            type="number"
            id="biaya_pemasangan"
            name="biaya_pemasangan"
            class="border p-2 w-full rounded"
            value="0">
    </div>

    <div>
        <label class="block mb-1 font-medium">
            Biaya Pembongkaran
        </label>

        <input
            type="number"
            id="biaya_pembongkaran"
            name="biaya_pembongkaran"
            class="border p-2 w-full rounded"
            value="0">
    </div>

    <div>
        <label class="block mb-1 font-medium">
            Biaya Pengiriman
        </label>

        <input
            type="number"
            id="biaya_pengiriman"
            name="biaya_pengiriman"
            class="border p-2 w-full rounded"
            value="0">
    </div>

    <div>
        <label class="block mb-1 font-medium">
            Biaya Lainnya
        </label>

        <input
            type="number"
            id="biaya_lainnya"
            name="biaya_lainnya"
            class="border p-2 w-full rounded"
            value="0">
    </div>

    <div>
        <label class="block mb-1 font-medium">
            Total Tagihan
        </label>

        <input
            type="number"
            id="total_tagihan"
            name="total_tagihan"
            class="border p-2 w-full rounded bg-gray-100"
            readonly>
    </div>

</div>

</div>


        {{-- CATATAN --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Catatan</label>
            <textarea name="notes"
                class="border p-2 w-full rounded"
                placeholder="Opsional"></textarea>
        </div>

        {{-- BUTTON --}}
        <div class="flex justify-end gap-2">

            <a href="{{ route('admin.payments.index') }}"
                class="px-4 py-2 border rounded">
                Batal
            </a>

            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Simpan Tagihan
            </button>

        </div>

    </form>

</div>

<script>
function hitungTotal() {

    let sewa =
        parseInt(document.getElementById('biaya_sewa').value) || 0;

    let pemasangan =
        parseInt(document.getElementById('biaya_pemasangan').value) || 0;

    let pembongkaran =
        parseInt(document.getElementById('biaya_pembongkaran').value) || 0;

    let pengiriman =
        parseInt(document.getElementById('biaya_pengiriman').value) || 0;

    let lainnya =
        parseInt(document.getElementById('biaya_lainnya').value) || 0;

    document.getElementById('total_tagihan').value =
        sewa +
        pemasangan +
        pembongkaran +
        pengiriman +
        lainnya;
}

document.querySelectorAll('input[type=number]')
    .forEach(input => {
        input.addEventListener('input', hitungTotal);
    });

hitungTotal();
</script>

@endsection