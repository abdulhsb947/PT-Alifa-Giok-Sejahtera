@extends('layouts.admin')

@section('content')

@php
    $statusMap = [
        'menunggu' => ['label' => 'Menunggu Perjanjian', 'class' => 'bg-yellow-100 text-yellow-700'],
        'siap_disewakan' => ['label' => 'Siap Disewakan', 'class' => 'bg-blue-100 text-blue-700'],
        'disewakan' => ['label' => 'Sedang Disewa', 'class' => 'bg-green-100 text-green-700'],
        'pengembalian' => ['label' => 'Pengembalian', 'class' => 'bg-indigo-100 text-indigo-700'],
        'terlambat' => ['label' => 'Terlambat', 'class' => 'bg-red-100 text-red-700'],
        'selesai' => ['label' => 'Selesai', 'class' => 'bg-gray-200 text-gray-700'],
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pemesanan & Penyewaan Offline</h1>
            <p class="text-sm text-gray-500">Kelola pemesanan, perjanjian, aktivasi sewa, dan pengembalian.</p>
        </div>

        <a href="{{ route('offline-orders.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
            Tambah Pemesanan
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg bg-red-100 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($orders as $order)
            @php
                $status = $statusMap[$order->status] ?? [
                    'label' => ucfirst(str_replace('_', ' ', $order->status)),
                    'class' => 'bg-gray-100 text-gray-700',
                ];
            @endphp

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900">{{ $order->order_code }}</h2>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                        <p class="mt-1 font-medium text-gray-700">{{ $order->customer_name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->phone }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('offline-orders.show', $order->id) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-blue-500 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-50">
                            Detail
                        </a>

                        @if($order->status == 'menunggu')
                            <a href="{{ route('offline-orders.show', $order->id) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-600">
                                Lengkapi Perjanjian
                            </a>
                        @endif

                        @if($order->status == 'siap_disewakan')
                            <form action="{{ route('offline-orders.activate', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                    Aktifkan Sewa
                                </button>
                            </form>
                        @endif

                        @if($order->status == 'disewakan')
                            <a href="{{ route('offline-orders.check-return', $order->id) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Proses Pengembalian
                            </a>
                        @endif
                    </div>
                </div>

                <div class="mt-5 grid gap-4 border-t border-gray-100 pt-4 text-sm sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <p class="text-gray-500">Proyek</p>
                        <p class="font-semibold text-gray-900">{{ $order->project_name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Produk</p>
                        <p class="font-semibold text-gray-900">{{ $order->items->count() }} item</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Durasi</p>
                        <p class="font-semibold text-gray-900">{{ $order->duration }} {{ $order->duration_unit }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Mulai</p>
                        <p class="font-semibold text-gray-900">{{ $order->start_date }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Total Barang</p>
                        <p class="font-bold text-blue-600">Rp {{ number_format($order->total_price) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="font-semibold text-gray-700">Belum ada pemesanan offline</p>
                <p class="mt-1 text-sm text-gray-500">Pemesanan yang dibuat admin akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
