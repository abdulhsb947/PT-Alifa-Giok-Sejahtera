@extends('layouts.customer')

@section('content')

<div class="p-6 max-w-3xl mx-auto bg-white rounded-xl shadow">

    <h1 class="text-xl font-bold mb-4">Edit Pesanan</h1>

    <form method="POST" action="/customer/orders/{{ $order->id }}/update">
        @csrf

        <!-- NAMA PROYEK -->
        <div class="mb-3">
            <label>Nama Proyek</label>
            <input type="text" name="project_name"
                   value="{{ $order->project_name }}"
                   class="border w-full p-2 rounded"
                   placeholder="Masukkan nama proyek">
        </div>

        <!-- LOKASI -->
        <div class="mb-3">
            <label>Lokasi Proyek</label>
            <textarea name="project_location"
                      class="border w-full p-2 rounded"
                      placeholder="Masukkan lokasi proyek">{{ $order->project_location }}</textarea>
        </div>

        <!-- TANGGAL MULAI -->
        <div class="mb-3">
            <label>Tanggal Mulai</label>
            <input type="date" name="start_date"
                   value="{{ $order->start_date }}"
                   class="border w-full p-2 rounded">
        </div>

        <!-- DURASI -->
        <div class="mb-3">
            <label>Durasi (Bulan)</label>
            <input type="number" name="duration"
                   value="{{ $order->duration }}"
                   class="border w-full p-2 rounded"
                   placeholder="Masukkan durasi sewa">
        </div>

        <!-- CATATAN -->
        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="notes"
                      class="border w-full p-2 rounded"
                      placeholder="Tambahkan catatan jika diperlukan">{{ $order->notes }}</textarea>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3 mt-4">

            <a href="/customer/orders"
                class="px-4 py-2 border rounded hover:bg-gray-100">
                Batal
            </a>

            <button class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection