@extends('layouts.customer')

@section('content')

<div class="p-6 max-w-5xl mx-auto">

    <h2 class="text-xl font-bold mb-4">Preview Dokumen Perjanjian</h2>

    @php
    $file = $order->agreement->file;
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    @endphp

    {{-- ===================== --}}
    {{-- FILE PDF / IMAGE --}}
    {{-- ===================== --}}
    @if(in_array(strtolower($ext), ['pdf','jpg','jpeg','png']))

    <div class="bg-white p-4 rounded-xl shadow">

        <iframe 
            src="{{ asset('storage/'.$file) }}"
            class="w-full h-[600px] border rounded mb-4">
        </iframe>

        <div class="flex justify-between items-center">

            <p class="text-sm text-gray-500">
                Format: {{ strtoupper($ext) }}
            </p>

            <a href="{{ asset('storage/'.$file) }}" download
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                ⬇ Unduh Dokumen
            </a>

        </div>

    </div>

    {{-- ===================== --}}
    {{-- FILE TIDAK DIDUKUNG --}}
    {{-- ===================== --}}
    @else

    <div class="bg-yellow-100 p-4 rounded">

        <p class="mb-2 font-medium">
            Preview tidak tersedia untuk file tipe {{ strtoupper($ext) }}
        </p>

        <p class="text-sm text-gray-600 mb-4">
            Silakan unduh file untuk melihat isi dokumen.
        </p>

        <a href="{{ asset('storage/'.$file) }}" download
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            ⬇ Unduh Dokumen
        </a>

    </div>

    @endif

</div>

@endsection