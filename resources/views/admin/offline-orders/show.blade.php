@extends('layouts.admin')

@section('content')


@php

$repairTotal =

collect($order->items)
->sum(function($item){

return (int)
$item->repair_cost;

});

$lostTotal =

collect($order->items)
->sum(function($item){

return (int)
$item->lost_cost;

});

$tax =
$order->agreement?->tax ?? 0;

$adminFee =
$order->agreement?->admin_fee ?? 0;

$shippingFee =
$order->agreement?->shipping_fee ?? 0;

$otherFee =
$order->agreement?->other_fee ?? 0;

$extraTotal =

$tax
+
$adminFee
+
$shippingFee
+
$otherFee;

$initialTotal =

(int) ($order->total_price ?? 0)

+

(int) $extraTotal;

$returnChargeTotal =

(int) ($order->penalty ?? 0)

+

(int) $repairTotal

+

(int) $lostTotal;

$remainingPayment =

$order->agreement

? (int) ($order->agreement->remaining_payment ?? 0)

: (int) $initialTotal;

$finalPaymentTotal =

(int) $remainingPayment

+

(int) $returnChargeTotal;

$grandTotal =

(int) $initialTotal

+

(int) $returnChargeTotal;

$hasReturnData =

$order->items
->whereNotNull('returned_qty')
->count();

$agreementCompleted =

$order->agreement

&&

$order->agreement->requirement_type

&&

$order->agreement->requirement_file

&&

$order->agreement->agreement_file

&&

$order->agreement->payment_type

&&

$order->agreement->payment_amount !== null;

$statusMap = [
    'menunggu' => ['label' => 'Menunggu Perjanjian', 'class' => 'bg-yellow-100 text-yellow-700'],
    'siap_disewakan' => ['label' => 'Siap Disewakan', 'class' => 'bg-blue-100 text-blue-700'],
    'disewakan' => ['label' => 'Sedang Disewa', 'class' => 'bg-green-100 text-green-700'],
    'pengembalian' => ['label' => 'Pengembalian', 'class' => 'bg-indigo-100 text-indigo-700'],
    'terlambat' => ['label' => 'Terlambat', 'class' => 'bg-red-100 text-red-700'],
    'selesai' => ['label' => 'Selesai', 'class' => 'bg-gray-200 text-gray-700'],
];

$currentStatus = $statusMap[$order->status] ?? [
    'label' => ucfirst(str_replace('_', ' ', $order->status)),
    'class' => 'bg-gray-100 text-gray-700',
];

@endphp

<div class="space-y-6">

    <!-- HEADER -->
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('offline-orders.index') }}"
                    class="text-sm font-medium text-gray-500 hover:text-blue-600">
                    Kembali ke daftar
                </a>

                <h1 class="mt-2 text-2xl font-bold text-gray-900">
                    Detail Penyewaan
                </h1>

                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span>{{ $order->order_code }}</span>
                    <span class="hidden sm:inline">/</span>
                    <span>{{ $order->customer_name }}</span>
                </div>
            </div>

            <div class="flex flex-col items-start gap-2 md:items-end">
                <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $currentStatus['class'] }}">
                    {{ $currentStatus['label'] }}
                </span>

                <p class="text-sm text-gray-500">
                    Total: <span class="font-bold text-blue-600">Rp {{ number_format($grandTotal) }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))

    <div class="bg-green-100 text-green-700
                    px-4 py-3 rounded-xl mb-4">

        {{ session('success') }}

    </div>

    @endif

    <!-- ERROR -->
    @if(session('error'))

    <div class="bg-red-100 text-red-700
                    px-4 py-3 rounded-xl mb-4">

        {{ session('error') }}

    </div>

    @endif

    @if($errors->any())

    <div class="bg-red-100 text-red-700
                    px-4 py-3 rounded-xl mb-4">

        <p class="font-semibold mb-2">

            Data belum bisa disimpan:

        </p>

        <ul class="list-disc pl-5 space-y-1">

            @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif

    <div class="grid lg:grid-cols-3 gap-6">

        <!-- LEFT -->
        <div class="lg:col-span-2 space-y-6">

            <!-- CUSTOMER -->
            <div class="bg-white border
                        rounded-lg p-5">

                <h2 class="font-bold text-lg mb-4">

                    Data Penyewa

                </h2>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>

                        <p class="text-sm text-gray-500">

                            Nama Customer

                        </p>

                        <p class="font-semibold">

                            {{ $order->customer_name }}

                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-gray-500">

                            No HP

                        </p>

                        <p class="font-semibold">

                            {{ $order->phone }}

                        </p>

                    </div>

                </div>

            </div>

            <!-- PROJECT -->
            <div class="bg-white border
                        rounded-lg p-5">

                <h2 class="font-bold text-lg mb-4">

                    Data Proyek

                </h2>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>

                        <p class="text-sm text-gray-500">

                            Nama Proyek

                        </p>

                        <p class="font-semibold">

                            {{ $order->project_name }}

                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-gray-500">

                            Tanggal Mulai

                        </p>

                        <p class="font-semibold">

                            {{ $order->start_date }}

                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-gray-500">

                            Durasi

                        </p>

                        <p class="font-semibold">

                            {{ $order->duration }}
                            {{ $order->duration_unit }}

                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-gray-500">

                            Lokasi

                        </p>

                        <p class="font-semibold">

                            {{ $order->project_location }}

                        </p>

                    </div>

                </div>

                <!-- NOTES -->
                @if($order->notes)

                <div class="mt-4">

                    <p class="text-sm text-gray-500">

                        Catatan

                    </p>

                    <p class="font-semibold">

                        {{ $order->notes }}

                    </p>

                </div>

                @endif

            </div>

            <!-- PRODUK -->
            <div class="bg-white border
                        rounded-lg p-5">

                <h2 class="font-bold text-lg mb-4">

                    Produk Disewa

                </h2>

                <div class="space-y-4">

                    @foreach($order->items as $item)

                    <div class="border rounded-xl
                                p-4 flex
                                justify-between
                                items-center">

                        <!-- LEFT -->
                        <div>

                            <h3 class="font-semibold">

                                {{ $item->product->name }}

                            </h3>

                            <p class="text-sm text-gray-500">

                                Qty:
                                {{ $item->quantity }}

                            </p>

                            <p class="text-sm text-blue-600">

                                Rp
                                {{ number_format($item->price) }}
                                / bulan

                            </p>

                        </div>

                        <!-- RIGHT -->
                        <div class="text-right">

                            <p class="text-sm text-gray-500">

                                Subtotal

                            </p>

                            <p class="font-bold">

                                Rp
                                {{ number_format(
                                    $item->quantity * $item->price
                                ) }}

                            </p>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>

            @if(in_array($order->status, ['pengembalian', 'terlambat']))

            <div class="bg-white border rounded-lg p-6">

                <h2 class="text-xl font-bold mb-5">

                    Pengecekan Barang

                </h2>

                <form action="{{ route('offline-orders.return', $order->id) }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    <!-- DENDA -->
                    <div class="border rounded-xl
                    p-5 mb-6
                    bg-red-50">

                        <h3 class="font-bold
                       text-red-600
                       mb-4">

                            Denda Keterlambatan

                        </h3>

                        <div class="grid md:grid-cols-2 gap-4">

                            <!-- HARI -->
                            <div>

                                <label class="block mb-2">

                                    Jumlah Hari Terlambat

                                </label>

                                <input type="number"
                                    name="late_days"
                                    min="0"
                                    value="{{ old('late_days', $order->late_days ?? 0) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                            <!-- DENDA -->
                            <div>

                                <label class="block mb-2">

                                    Nominal Denda (Rp)

                                </label>

                                <input type="number"
                                    name="penalty"
                                    min="0"
                                    value="{{ old('penalty', $order->penalty ?? 0) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                        </div>

                    </div>


                    @foreach($order->items as $item)

                    <div class="border rounded-xl
                    p-5 mb-5">

                        <!-- HEADER -->
                        <div class="mb-5">

                            <h3 class="font-bold text-lg">

                                {{ $item->product->name }}

                            </h3>

                            <p class="text-gray-500">

                                Qty Disewa:
                                {{ $item->quantity }}

                            </p>

                        </div>

                        <!-- FORM -->
                        <div class="grid md:grid-cols-2 gap-4">

                            <!-- KEMBALI -->
                            <div>

                                <label class="block mb-2">

                                    Barang Kembali

                                </label>

                                <input type="number"
                                    name="returned_qty[{{ $item->id }}]"
                                    min="0"
                                    value="{{ old('returned_qty.'.$item->id, $item->returned_qty ?? $item->quantity) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                            <!-- RUSAK -->
                            <div>

                                <label class="block mb-2">

                                    Barang Rusak

                                </label>

                                <input type="number"
                                    name="damaged_qty[{{ $item->id }}]"
                                    min="0"
                                    value="{{ old('damaged_qty.'.$item->id, $item->damaged_qty ?? 0) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                            <!-- HILANG -->
                            <div>

                                <label class="block mb-2">

                                    Barang Hilang

                                </label>

                                <input type="number"
                                    name="lost_qty[{{ $item->id }}]"
                                    min="0"
                                    value="{{ old('lost_qty.'.$item->id, $item->lost_qty ?? 0) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                            <!-- PERBAIKAN -->
                            <div>

                                <label class="block mb-2">

                                    Biaya Perbaikan (Rp)

                                </label>

                                <input type="number"
                                    name="repair_cost[{{ $item->id }}]"
                                    min="0"
                                    value="{{ old('repair_cost.'.$item->id, $item->repair_cost ?? 0) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                            <!-- KEHILANGAN -->
                            <div>

                                <label class="block mb-2">

                                    Biaya Kehilangan (Rp)

                                </label>

                                <input type="number"
                                    name="lost_cost[{{ $item->id }}]"
                                    min="0"
                                    value="{{ old('lost_cost.'.$item->id, $item->lost_cost ?? 0) }}"
                                    class="w-full border
                                  rounded-xl
                                  px-3 py-2">

                            </div>

                        </div>

                        <!-- CATATAN -->
                        <div class="mt-4">

                            <label class="block mb-2">

                                Catatan Pengecekan

                            </label>

                            <textarea
                                name="return_notes[{{ $item->id }}]"
                                rows="3"
                                class="w-full border
                           rounded-xl
                           px-3 py-2">{{ old('return_notes.'.$item->id, $item->return_notes) }}</textarea>

                        </div>

                    </div>

                    @endforeach

                    <!-- BUTTON -->
                    <button type="submit"
                        class="w-full bg-blue-600
                       hover:bg-blue-700
                       text-white py-3
                       rounded-xl">

                        Update Pengecekan

                    </button>

                </form>

            </div>

            @endif

        </div>

        <!-- DOKUMEN & PERJANJIAN -->
        <!-- RIGHT -->
        <div>

            <!-- DOKUMEN -->
            <div class="bg-white border
                rounded-lg p-5">

                <h2 class="font-bold text-lg mb-4">

                    Dokumen & Pembayaran

                </h2>

                @if(!$agreementCompleted)

                <!-- FORM -->
                <form action="{{ route('offline-orders.agreement', $order->id) }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="space-y-4">

                        <!-- TYPE -->
                        <div>

                            <label class="font-semibold">

                                Jenis Dokumen

                            </label>

                            <div class="flex gap-5 mt-3">

                                <label>

                                    <input type="radio"
                                        name="requirement_type"
                                        value="npwp"
                                        @checked(old('requirement_type')=='npwp' )>

                                    NPWP

                                </label>

                                <label>

                                    <input type="radio"
                                        name="requirement_type"
                                        value="spk"
                                        @checked(old('requirement_type')=='spk' )>

                                    SPK

                                </label>

                                <label>

                                    <input type="radio"
                                        name="requirement_type"
                                        value="ktp"
                                        @checked(old('requirement_type')=='ktp' )>

                                    KTP

                                </label>

                            </div>

                        </div>

                        <!-- FILE -->
                        <div>

                            <label>

                                Upload Dokumen

                            </label>

                            <input type="file"
                                name="requirement_file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border
                                  rounded-xl px-3 py-2">

                        </div>

                        <!-- AGREEMENT -->
                        <div>

                            <label>

                                Surat Perjanjian

                            </label>

                            <input type="file"
                                name="agreement_file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border
                                  rounded-xl px-3 py-2">

                        </div>

                        <!-- PAYMENT -->
                        <div>

                            <label>

                                Tipe Pembayaran

                            </label>

                            <select name="payment_type"
                                class="w-full border
                                   rounded-xl px-3 py-2">

                                <option value="dp"
                                    @selected(old('payment_type')=='dp' )>

                                    DP

                                </option>

                                <option value="lunas"
                                    @selected(old('payment_type')=='lunas' )>

                                    Lunas

                                </option>

                            </select>

                        </div>

                        <!-- BIAYA TAMBAHAN AWAL -->
                        <div class="grid md:grid-cols-2 gap-4">

                            <div>

                                <label>Pemasangan</label>

                                <input type="number"
                                    id="tax"
                                    name="tax"
                                    value="{{ old('tax', $tax) }}"
                                    class="w-full border rounded-xl px-3 py-2">

                            </div>

                            <div>

                                <label>Pembongkaran</label>

                                <input type="number"
                                    id="admin_fee"
                                    name="admin_fee"
                                    value="{{ old('admin_fee', $adminFee) }}"
                                    class="w-full border rounded-xl px-3 py-2">

                            </div>

                            <div>

                                <label>Biaya Pengiriman</label>

                                <input type="number"
                                    id="shipping_fee"
                                    name="shipping_fee"
                                    value="{{ old('shipping_fee', $shippingFee) }}"
                                    class="w-full border rounded-xl px-3 py-2">

                            </div>

                            <div>

                                <label>Biaya Lain-lain</label>

                                <input type="number"
                                    id="other_fee"
                                    name="other_fee"
                                    value="{{ old('other_fee', $otherFee) }}"
                                    class="w-full border rounded-xl px-3 py-2">

                            </div>

                        </div>

                        <input type="hidden"
                            id="agreement_base_total"
                            value="{{ $order->total_price ?? 0 }}">

                        <div class="bg-gray-50 border rounded-xl p-4">

                            <div class="flex justify-between font-bold">

                                <span>Total Tagihan Awal</span>

                                <span id="agreement_total">

                                    Rp {{ number_format($initialTotal) }}

                                </span>

                            </div>

                        </div>

                        <!-- AMOUNT -->
                        <div>

                            <label>

                                Nominal Pembayaran

                            </label>

                            <input type="number"
                                id="agreement_payment_amount"
                                name="payment_amount"
                                value="{{ old('payment_amount', $initialTotal) }}"
                                class="w-full border
                                  rounded-xl px-3 py-2">

                        </div>

                        <!-- PROOF -->
                        <div>

                            <label>

                                Bukti Pembayaran

                            </label>

                            <input type="file"
                                name="payment_proof"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border
                                  rounded-xl px-3 py-2">

                        </div>

                        <!-- NOTES -->
                        <div>

                            <label>

                                Catatan

                            </label>

                            <textarea name="notes"
                                rows="3"
                                class="w-full border
                                     rounded-xl px-3 py-2">{{ old('notes') }}</textarea>

                        </div>

                        <button type="submit"
                            class="w-full bg-yellow-500
                               hover:bg-yellow-600
                               text-white py-3
                               rounded-xl">

                            Simpan Perjanjian

                        </button>

                    </div>

                </form>

                @else

                <!-- HASIL -->
                <div class="space-y-5">

                    <div class="bg-green-100
                        text-green-700
                        px-4 py-3 rounded-xl">

                        Dokumen & Pembayaran berhasil diupload

                    </div>

                    <!-- JENIS -->
                    <div>

                        <p class="text-sm text-gray-500">

                            Jenis Dokumen

                        </p>

                        <p class="font-semibold uppercase">

                            {{ $order->agreement?->requirement_type }}

                        </p>

                    </div>

                    <!-- FILE -->
                    <div>

                        <a href="{{ asset('storage/'.$order->agreement?->requirement_file) }}"
                            target="_blank"
                            class="text-blue-600 underline">

                            Lihat Dokumen

                        </a>

                    </div>

                    <!-- AGREEMENT -->
                    <div>

                        <a href="{{ asset('storage/'.$order->agreement?->agreement_file) }}"
                            target="_blank"
                            class="text-blue-600 underline">

                            Lihat Surat Perjanjian

                        </a>

                    </div>

                    <!-- PAYMENT -->
                    <div>

                        <p class="text-sm text-gray-500">

                            Pembayaran

                        </p>

                        <p class="font-semibold uppercase">

                            {{ $order->agreement?->payment_type }}

                        </p>

                    </div>

                    <!-- AMOUNT -->
                    <div>

                        <p class="text-sm text-gray-500">

                            Nominal

                        </p>

                        <p class="font-bold text-green-600">

                            Rp
                            {{ number_format($order->agreement?->payment_amount) }}

                        </p>

                    </div>

                    <!-- PROOF -->
                    <div>

                        <a href="{{ asset('storage/'.$order->agreement?->payment_proof) }}"
                            target="_blank"
                            class="text-blue-600 underline">

                            Lihat Bukti Pembayaran

                        </a>

                    </div>

                </div>



            </div>

            <!-- CATATAN -->
            @if($order->agreement?->notes)

            <div class="border rounded-xl p-4">

                <h3 class="font-bold mb-3">

                    Catatan

                </h3>

                <p class="text-gray-700">

                    {{ $order->agreement?->notes }}

                </p>

            </div>
            @endif
            @endif
            <!-- RIGHT -->


            <!-- ACTION -->
            <div class="mt-6 space-y-6">

                <!-- AKTIFKAN -->
                @if($order->status == 'siap_disewakan')

                <form action="{{ route('offline-orders.activate', $order->id) }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    <button type="submit"
                        class="w-full bg-green-600
                               hover:bg-green-700
                               text-white py-3
                               rounded-xl">

                        Aktifkan Penyewaan

                    </button>

                </form>

                @endif
            </div>



                <div class="bg-white border
                rounded-lg p-5">

                    <h2 class="font-bold text-lg mb-4">

                        Ringkasan

                    </h2>

                    <div class="space-y-4">

                        <!-- TOTAL -->
                        <div class="flex justify-between">

                            <span>Total Harga Barang</span>

                            <span class="font-bold
                             text-blue-600">

                                Rp
                                {{ number_format($order->total_price) }}

                            </span>

                        </div>

                        @if($order->agreement)

                        <div class="border-t pt-4 space-y-3">

                            <div class="flex justify-between">

                                <span>Pemasangan</span>

                                <span>

                                    Rp
                                    {{ number_format($tax) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Pembongkaran</span>

                                <span>

                                    Rp
                                    {{ number_format($adminFee) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Biaya Pengiriman</span>

                                <span>

                                    Rp
                                    {{ number_format($shippingFee) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Biaya Lain-lain</span>

                                <span>

                                    Rp
                                    {{ number_format($otherFee) }}

                                </span>

                            </div>

                            <div class="flex justify-between
                                font-bold text-green-600">

                                <span>Total Tagihan Awal</span>

                                <span>

                                    Rp
                                    {{ number_format($initialTotal) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Pembayaran Awal</span>

                                <span class="font-semibold">

                                    Rp
                                    {{ number_format($order->agreement?->payment_amount ?? 0) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Sisa Pembayaran</span>

                                <span class="font-semibold
                                     text-red-600">

                                    Rp
                                    {{ number_format($remainingPayment) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Status Pembayaran</span>

                                @if($remainingPayment <= 0)

                                    <span class="bg-green-100
                                     text-green-700
                                     px-3 py-1 rounded-full
                                     text-sm font-semibold">

                                    Lunas

                                    </span>

                                    @else

                                    <span class="bg-yellow-100
                                     text-yellow-700
                                     px-3 py-1 rounded-full
                                     text-sm font-semibold">

                                        DP

                                    </span>

                                    @endif

                            </div>

                        </div>

                        @endif

                        <!-- STATUS -->
                        <div class="flex justify-between">

                            <span>Status</span>

                            @if($order->status == 'menunggu')

                            <span class="bg-yellow-100
                             text-yellow-700
                             px-3 py-1 rounded-full
                             text-sm font-semibold">

                                Menunggu Dokumen

                            </span>

                            @elseif($order->status == 'siap_disewakan')

                            <span class="bg-blue-100
                             text-blue-700
                             px-3 py-1 rounded-full
                             text-sm font-semibold">

                                Siap Disewakan

                            </span>

                            @elseif($order->status == 'disewakan')

                            <span class="bg-green-100
                             text-green-700
                             px-3 py-1 rounded-full
                             text-sm font-semibold">

                                Sedang Disewakan

                            </span>

                            @elseif($order->status == 'pengembalian')

                            <span class="bg-indigo-100
                             text-indigo-700
                             px-3 py-1 rounded-full
                             text-sm font-semibold">

                                Pengembalian

                            </span>

                            @elseif($order->status == 'terlambat')

                            <span class="bg-red-100
                             text-red-700
                             px-3 py-1 rounded-full
                             text-sm font-semibold">

                                Terlambat

                            </span>

                            @elseif($order->status == 'selesai')

                            <span class="bg-gray-200
                             text-gray-700
                             px-3 py-1 rounded-full
                             text-sm font-semibold">

                                Selesai

                            </span>
                            @endif
                        </div>


                        @if($order->status == 'disewakan')

                        @endif


                        @if(in_array($order->status, ['pengembalian', 'terlambat']))
                        <div class="bg-gray-50 border rounded-xl p-4 mb-5">


                            <div class="space-y-4">

                                @foreach($order->items as $item)

                                @if(

                                $item->damaged_qty > 0

                                ||

                                $item->lost_qty > 0

                                ||

                                $item->repair_cost > 0

                                ||

                                $item->lost_cost > 0

                                )

                                <div class="border rounded-lg
                    p-4">

                                    <div class="flex
                        justify-between">

                                        <div>

                                            <h4 class="font-semibold">

                                                {{ $item->product->name }}

                                            </h4>

                                            <div class="mt-2
                                text-sm space-y-1">

                                                <p>

                                                    Barang Rusak:
                                                    {{ $item->damaged_qty }}

                                                </p>

                                                <p>

                                                    Barang Hilang:
                                                    {{ $item->lost_qty }}

                                                </p>

                                            </div>

                                        </div>

                                        <div class="text-right">

                                            <p class="text-orange-600
                              font-semibold">

                                                Perbaikan:
                                                Rp
                                                {{ number_format(
                            $item->repair_cost
                        ) }}

                                            </p>

                                            <p class="text-red-600
                              font-semibold mt-2">

                                                Kehilangan:
                                                Rp
                                                {{ number_format(
                            $item->lost_cost
                        ) }}

                                            </p>

                                        </div>

                                    </div>

                                    @if($item->return_notes)

                                    <div class="mt-3
                        bg-white
                        p-3 rounded-lg">

                                        <p class="text-sm
                          text-gray-600">

                                            {{ $item->return_notes }}

                                        </p>

                                    </div>

                                    @endif

                                </div>

                                @endif

                                @endforeach

                            </div>

                        </div>

                        @endif
                        <!-- PELUNASAN -->

                        @if(in_array($order->status, ['pengembalian', 'terlambat']) && $finalPaymentTotal <= 0)

                            <div class="bg-green-100
                        text-green-700
                        px-4 py-3 rounded-xl">

                            Pembayaran sudah lunas. Jika pengecekan tidak menemukan
                            denda, kerusakan, atau kehilangan, penyewaan akan langsung
                            selesai setelah Update Pengecekan.

                    </div>

                    @endif

                    @if(in_array($order->status, ['pengembalian', 'terlambat']) && $finalPaymentTotal > 0)

                    <div class="bg-white border
                        rounded-lg p-5">

                        <h2 class="font-bold text-lg mb-4">

                            Pelunasan & Finalisasi

                        </h2>

                        <!-- TOTAL -->
                        <div class="space-y-3 mb-5">

                            <div class="flex justify-between">

                                <span>Sisa Pembayaran Awal</span>

                                <span>

                                    Rp
                                    {{ number_format($remainingPayment) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Denda Keterlambatan</span>

                                <span>

                                    Rp
                                    {{ number_format($order->penalty ?? 0) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Biaya Kerusakan</span>

                                <span>

                                    Rp
                                    {{ number_format($repairTotal) }}

                                </span>

                            </div>

                            <div class="flex justify-between">

                                <span>Biaya Kehilangan</span>

                                <span>

                                    Rp
                                    {{ number_format($lostTotal) }}

                                </span>

                            </div>

                            <div class="flex justify-between
            font-bold text-red-600">

                                <span>Total Tagihan</span>

                                <span id="totalTagihan">

                                    Rp {{ number_format((int) $finalPaymentTotal) }}

                                </span>

                            </div>

                        </div>

                        <input type="hidden"
                            id="baseTotal"
                            value="{{ $finalPaymentTotal }}">

                        <!-- FORM -->
                        <form action="{{ route('offline-orders.final-payment', $order->id) }}"
                            method="POST"
                            enctype="multipart/form-data">

                            @csrf

                            <div class="space-y-4">

                                <!-- SISA -->
                                <div>
                                    <label>

                                        Sisa Pembayaran / Tagihan Tambahan

                                    </label>

                                    <input type="number"
                                        id="payment_amount"
                                        name="payment_amount"

                                        value="{{ $finalPaymentTotal }}"

                                        class="w-full border
                                          rounded-xl px-3 py-2">

                                </div>

                                <!-- FILE -->
                                <div>

                                    <label>

                                        Bukti Pelunasan

                                    </label>

                                    <input type="file"
                                        name="final_payment_proof"

                                        class="w-full border
                                          rounded-xl px-3 py-2">

                                </div>

                                <button type="submit"

                                    class="w-full bg-green-600
                                       hover:bg-green-700
                                       text-white py-3
                                       rounded-xl">

                                    Finalisasi Penyewaan

                                </button>

                            </div>

                        </form>

                    </div>

                    @endif

                    <!-- BUKTI PELUNASAN TERAKHIR -->
                    @if($order->agreement?->final_payment_proof)

                    <div class="border rounded-xl
            p-4 bg-green-50">

                        <h3 class="font-bold mb-3
               text-green-700">

                            Bukti Pelunasan Terakhir

                        </h3>

                        <a href="{{ asset('storage/'.$order->agreement?->final_payment_proof) }}"
                            target="_blank"
                            class="text-blue-600 underline">

                            Lihat Bukti Pelunasan

                        </a>

                    </div>

                    @endif


                    <!-- SELESAI -->
                    @if($order->status == 'selesai')

                    <div class="bg-green-100
                        text-green-700
                        px-4 py-3 rounded-xl">

                        Penyewaan telah selesai

                    </div>

                    @endif

                    <!-- KEMBALI -->
                    <a href="{{ route('offline-orders.index') }}"
                        class="block w-full text-center
                      border border-gray-300
                      hover:bg-gray-100
                      py-3 rounded-xl">

                        Kembali

                    </a>

                </div>

            </div>

        </div>
    </div>

</div>


<script>
    function formatRupiah(value) {
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    function calculateAgreementTotal() {
        let baseInput =
            document.getElementById('agreement_base_total');

        let paymentInput =
            document.getElementById('agreement_payment_amount');

        let totalLabel =
            document.getElementById('agreement_total');

        if (!baseInput || !paymentInput || !totalLabel) {
            return;
        }

        let baseTotal =
            parseInt(baseInput.value) || 0;

        let tax =
            parseInt(
                document.getElementById('tax').value
            ) || 0;

        let admin =
            parseInt(
                document.getElementById('admin_fee').value
            ) || 0;

        let shipping =
            parseInt(
                document.getElementById('shipping_fee').value
            ) || 0;

        let other =
            parseInt(
                document.getElementById('other_fee').value
            ) || 0;

        let grandTotal =

            baseTotal +
            tax +
            admin +
            shipping +
            other;

        totalLabel.innerHTML =

            formatRupiah(grandTotal);

        let summaryTax =
            document.getElementById('summary_tax');

        let summaryAdmin =
            document.getElementById('summary_admin_fee');

        let summaryShipping =
            document.getElementById('summary_shipping_fee');

        let summaryOther =
            document.getElementById('summary_other_fee');

        let summaryGrandTotal =
            document.getElementById('summary_grand_total');

        if (summaryTax) {
            summaryTax.innerHTML = formatRupiah(tax);
        }

        if (summaryAdmin) {
            summaryAdmin.innerHTML = formatRupiah(admin);
        }

        if (summaryShipping) {
            summaryShipping.innerHTML = formatRupiah(shipping);
        }

        if (summaryOther) {
            summaryOther.innerHTML = formatRupiah(other);
        }

        if (summaryGrandTotal) {
            summaryGrandTotal.innerHTML = formatRupiah(grandTotal);
        }

        paymentInput.value = grandTotal;
    }

    window.addEventListener(
        'load',
        calculateAgreementTotal
    );

    [
        'tax',
        'admin_fee',
        'shipping_fee',
        'other_fee'
    ]
    .forEach(function(id) {

        let input =
            document.getElementById(id);

        if (input) {
            input.addEventListener(
                'input',
                calculateAgreementTotal
            );
        }

    });
</script>

@endsection
