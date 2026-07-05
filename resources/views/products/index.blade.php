@extends('layouts.app')

@section('content')

<div class="container mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Products</h1>

    <div class="grid grid-cols-3 gap-6">

        @forelse($products as $p)
        <div class="bg-white p-4 rounded shadow">

            <h2 class="font-bold text-lg">{{ $p->name }}</h2>

            <p class="text-gray-500">
                {{ $p->description }}
            </p>

            <!-- 🔥 UBAH KE BULAN -->
            <p class="text-blue-600 font-bold mt-2">
                Rp {{ number_format($p->price_per_month) }} /bulan
            </p>

            <p class="text-sm text-gray-500">
                Stock: {{ $p->available_stock }}
            </p>

        </div>
        @empty
            <p>Tidak ada produk</p>
        @endforelse

    </div>

</div>
@if($p->image)
    <img src="{{ asset('storage/'.$p->image) }}" 
         class="w-full h-40 object-cover rounded mb-2">
@endif

@endsection