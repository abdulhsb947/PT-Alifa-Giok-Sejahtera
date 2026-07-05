@extends('layouts.customer')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-2">
    Detail Tagihan Denda
</h2>

@if($isEdit ?? false)
<div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 rounded-lg">
    <p class="text-yellow-700 font-semibold">
        ⚠ Pembayaran denda sebelumnya ditolak.
    </p>

    <p class="text-sm text-gray-600">
        Silakan upload ulang bukti pembayaran yang benar.
    </p>
</div>
@endif

    {{-- ====================== --}}
    {{-- ORDER --}}
    {{-- ====================== --}}
    <div class="mb-6">

        <p>
            <strong>Kode Pesanan :</strong>
            {{ $penalty->rental->order->order_code }}
        </p>

        <p>
            <strong>Total Denda :</strong>
            Rp {{ number_format($penalty->total_fee) }}
        </p>

    </div>

    {{-- ====================== --}}
    {{-- KETERLAMBATAN --}}
    {{-- ====================== --}}
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-5">

        <h4 class="font-semibold text-red-600 mb-3">
            Denda Keterlambatan
        </h4>

        <div class="grid md:grid-cols-2 gap-4">

            <div>
                <p class="text-gray-500">
                    Hari Terlambat
                </p>

                <p class="font-semibold">
                    {{ $penalty->late_days }} Hari
                </p>
            </div>

            <div>
                <p class="text-gray-500">
                    Nominal
                </p>

                <p class="font-semibold text-red-600">
                    Rp {{ number_format($penalty->late_fee) }}
                </p>
            </div>

        </div>

    </div>

    {{-- ====================== --}}
    {{-- KERUSAKAN --}}
    {{-- ====================== --}}
    @if($penalty->rental->returnItems->count())

    <div class="mb-5">

        <h4 class="font-semibold text-red-600 mb-3">
            Detail Kerusakan / Kehilangan
        </h4>

        @foreach($penalty->rental->returnItems as $item)

        <div class="border rounded-lg p-4 mb-3">

            <h5 class="font-semibold mb-3">
                {{ $item->product->name }}
            </h5>

            <div class="grid md:grid-cols-4 gap-4 text-sm">

                <div>
                    <p class="text-gray-500">
                        Rusak
                    </p>

                    <p class="font-semibold">
                        {{ $item->damaged_qty }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">
                        Hilang
                    </p>

                    <p class="font-semibold">
                        {{ $item->lost_qty }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">
                        Biaya Perbaikan
                    </p>

                    <p class="font-semibold">
                        Rp {{ number_format($item->repair_cost) }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">
                        Biaya Kehilangan
                    </p>

                    <p class="font-semibold">
                        Rp {{ number_format($item->lost_cost) }}
                    </p>
                </div>

            </div>

            @if($item->notes)

            <div class="mt-3">

                <p class="text-gray-500">
                    Keterangan
                </p>

                <p>
                    {{ $item->notes }}
                </p>

            </div>

            @endif

        </div>

        @endforeach

    </div>

    @endif

    {{-- ====================== --}}
    {{-- TOTAL --}}
    {{-- ====================== --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-5">

        <p class="text-gray-600">
            Total Denda Yang Harus Dibayar
        </p>

        <p class="text-2xl font-bold text-red-600">
            Rp {{ number_format($penalty->total_fee) }}
        </p>

    </div>

    {{-- ====================== --}}
    {{-- UPLOAD --}}
    {{-- ====================== --}}
    <form
        method="POST"
        action="{{ route('penalty.pay', $penalty->id) }}"
        enctype="multipart/form-data">

        @csrf

        <div class="mb-4">

            <label class="block mb-2">
                Upload Bukti Pembayaran
            </label>

            <input
                type="file"
                name="bukti"
                required
                class="w-full border p-2 rounded">

        </div>

        <button
            type="submit"
            class="bg-red-500 text-white px-4 py-2 rounded">

            Kirim Bukti Pembayaran

        </button>

    </form>

</div>

@endsection