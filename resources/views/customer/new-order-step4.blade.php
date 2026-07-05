@extends('layouts.customer')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">

    <!-- VALIDASI SESSION -->
    @if(!session('order.products'))
    <div class="bg-red-500 text-white p-4 rounded text-center">
        Sesi telah berakhir. Silakan buat pesanan kembali.
    </div>
    @endif

    <!-- HEADER -->
    <div class="flex items-center gap-3 mb-6">
        <a href="/customer/orders/step3" class="text-gray-500">←</a>

        <div>
            <h1 class="text-xl md:text-2xl font-bold">Buat Pesanan Baru</h1>
            <p class="text-gray-500 text-sm">
                Periksa kembali pesanan Anda sebelum dikirim
            </p>
        </div>
    </div>

    <!-- STEPPER -->
    <div class="flex flex-wrap items-center gap-4 mb-8 text-sm">

        <div class="flex items-center gap-2 text-blue-600">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">✓</div>
            <span class="hidden sm:inline">Proyek</span>
        </div>

        <div class="flex-1 h-[2px] bg-blue-600 hidden sm:block"></div>

        <div class="flex items-center gap-2 text-blue-600">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">✓</div>
            <span class="hidden sm:inline">Produk</span>
        </div>

        <div class="flex-1 h-[2px] bg-blue-600 hidden sm:block"></div>

        <div class="flex items-center gap-2 text-blue-600">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">✓</div>
            <span class="hidden sm:inline">Dokumen</span>
        </div>

        <div class="flex-1 h-[2px] bg-blue-600 hidden sm:block"></div>

        <div class="flex items-center gap-2 text-blue-600 font-bold">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">4</div>
            <span>Review</span>
        </div>

    </div>

    <!-- INFO EDIT -->
    @if(session('order.edit_id'))
    <div class="bg-yellow-100 text-yellow-700 p-3 rounded mb-4 text-center">
        Anda sedang mengedit pesanan sebelumnya
    </div>
    @endif

    <!-- CARD -->
    <div class="bg-white rounded-xl shadow p-4 md:p-6 space-y-6">

        <!-- DETAIL PROYEK -->
        <div>
            <h2 class="font-bold mb-3">Detail Proyek</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                <div>
                    <p class="text-gray-400">Nama Proyek</p>
                    <p class="font-medium">{{ session('order.project_name') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-400">Lokasi</p>
                    <p class="font-medium">{{ session('order.project_location') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-400">No. Telepon</p>
                    <p class="font-medium">{{ session('order.phone') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-400">Tanggal Mulai</p>
                    <p class="font-medium">{{ session('order.start_date') ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-400">Durasi</p>
                    <p class="font-medium">{{ session('order.duration') ?? 0 }} bulan</p>
                </div>

                <div>
                    <p class="text-gray-400">Catatan</p>
                    <p class="text-gray-500">
                        {{ session('order.notes') ?? '-' }}
                    </p>
                </div>

            </div>
        </div>

        <!-- PRODUK -->
        <div>
            <h2 class="font-bold mb-3">
                Produk ({{ count(session('order.products') ?? []) }} item)
            </h2>

            <div class="space-y-3">

                @php $total = 0; @endphp

                @foreach(session('order.products') ?? [] as $id)

                @php
                $product = \App\Models\Product::find($id);
                if(!$product) continue;

                $qty = session('order.quantities')[$id] ?? 0;
                $duration = session('order.duration') ?? 1;

                $subtotal = $product->price_per_month * $qty * $duration;
                $total += $subtotal;
                @endphp

                <div class="flex flex-col md:flex-row md:items-center justify-between bg-gray-50 p-4 rounded-lg gap-3">

                    <div class="flex items-center gap-3">

                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/100' }}"
                            class="w-14 h-14 object-cover rounded">

                        <div>
                            <p class="font-medium">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">
                                Rp {{ number_format($product->price_per_month) }} × {{ $qty }} unit
                            </p>
                        </div>

                    </div>

                    <p class="text-blue-600 font-bold text-right">
                        Rp {{ number_format($subtotal) }}
                    </p>

                </div>

                @endforeach

            </div>
        </div>

        <!-- DOKUMEN -->
        <div class="flex items-center gap-3">
            <div class="bg-blue-100 text-blue-600 p-2 rounded">📄</div>
            <div>
                <p class="font-medium">Dokumen</p>
                <p class="text-gray-500">
                    @php $doc = session('order.document_type'); @endphp

                    @if($doc == 'npwp')
                        NPWP
                    @elseif($doc == 'spk')
                        SPK (Surat Perintah Kerja)
                    @elseif($doc == 'ktp')
                        KTP
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        <!-- TOTAL -->
        <div>
            <p class="font-medium">Estimasi Total</p>
            <p class="text-blue-600 text-lg font-bold">
                Rp {{ number_format($total) }}
            </p>
        </div>

    </div>

    <!-- WARNING -->
    @if(isset($order) && $order->status == 'ditolak')
    <div class="bg-red-100 text-red-600 p-3 rounded mb-4 mt-4">
        Pesanan sebelumnya ditolak, silakan perbaiki data sesuai kebutuhan
    </div>
    @endif

    <!-- BUTTON -->
    <form method="POST" 
        action="{{ session('order.edit_id') 
            ? '/customer/orders/' . session('order.edit_id') . '/update' 
            : '/customer/orders/store' }}">
            
        @csrf

        <div class="flex flex-col md:flex-row justify-between gap-3 mt-4">

            <a href="/customer/orders/step3"
                class="px-4 py-2 border rounded text-center">
                ← Sebelumnya
            </a>

            <button class="bg-blue-600 text-white px-6 py-2 rounded flex items-center justify-center gap-2">
                Kirim Pesanan ✓
            </button>

        </div>

    </form>

</div>

@endsection