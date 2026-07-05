@extends('layouts.customer')

@section('content')

<div class="max-w-6xl mx-auto p-6">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Pembayaran</h1>
        <p class="text-gray-500 text-sm">
            Unggah bukti pembayaran dan pantau status verifikasi
        </p>
    </div>

    <!-- SUMMARY -->
    <div class="grid md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">⏳</div>
            <div>
                <p class="text-lg font-bold">{{ $pending }}</p>
                <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">✔</div>
            <div>
                <p class="text-lg font-bold">{{ $approved }}</p>
                <p class="text-sm text-gray-500">Disetujui</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-4">
            <div class="bg-red-100 text-red-600 p-3 rounded-full">✖</div>
            <div>
                <p class="text-lg font-bold">{{ $rejected }}</p>
                <p class="text-sm text-gray-500">Ditolak</p>
            </div>
        </div>

    </div>

    <!-- LIST -->
    <div class="space-y-4">

@forelse($payments as $payment)

@php
$hasCustomerPayment = $payment->order
    ? $payment->order->payments
        ->whereIn('payment_type', ['dp','pelunasan','lunas'])
        ->count() > 0
    : false;
@endphp

@if($payment->payment_type == 'tagihan' && $hasCustomerPayment)
    @continue
@endif

<div class="bg-white p-4 md:p-5 rounded-xl shadow">

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <!-- LEFT -->
        <div class="flex flex-col gap-3 w-full">

            <!-- HEADER -->
            <div class="flex items-center gap-2 flex-wrap">

                <p class="font-bold text-sm md:text-base">
                    PAY-{{ $payment->id }}
                </p>

                <!-- STATUS -->
                <span class="flex items-center gap-1 px-2 py-1 text-xs rounded-full
                    @if($payment->status == 'disetujui') bg-green-100 text-green-600
                    @elseif($payment->status == 'ditolak') bg-red-100 text-red-600
                    @elseif($payment->status == 'menunggu_pembayaran') bg-blue-100 text-blue-600
                    @else bg-yellow-100 text-yellow-600
                    @endif
                ">
                    @if($payment->status == 'disetujui')
                        ✔ Disetujui
                    @elseif($payment->status == 'ditolak')
                        ✖ Ditolak
                    @elseif($payment->status == 'menunggu_pembayaran')
                        💳 Menunggu Pembayaran
                    @else
                        ⏳ Menunggu Verifikasi
                    @endif
                </span>

                <!-- TYPE -->
                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                    {{ strtoupper($payment->payment_type) }}
                </span>

            </div>

            <!-- INFO -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">

                <div>
                    <p class="text-xs text-gray-400">Pesanan</p>
                    <p class="font-medium">
                        {{ optional($payment->order)->order_code ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Pelanggan</p>
                    <p class="font-medium">
                        {{ optional(optional($payment->order)->user)->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Jumlah</p>
                    <p class="font-bold text-blue-600">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Sisa</p>
                    <p class="font-semibold text-gray-700">
                        Rp {{ number_format($payment->sisa_pembayaran ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Tanggal</p>
                    <p>
                        {{ $payment->created_at?->format('d M Y') }}
                    </p>
                </div>

            </div>

            @if($payment->notes)
                <div class="text-sm">
                    <p class="text-xs text-gray-400">Catatan</p>
                    <p class="text-gray-700">{{ $payment->notes }}</p>
                </div>
            @endif

        </div>

        <!-- RIGHT -->
        <div class="flex flex-col gap-2 items-start md:items-end">

            <!-- LINK -->
            <div class="flex gap-3 flex-wrap">

                @if(optional($payment->order)->agreement)
                    <a href="{{ asset('storage/'.optional($payment->order->agreement)->file) }}"
                       target="_blank"
                       class="text-blue-600 text-sm underline">
                       Perjanjian
                    </a>
                @endif

                @if($payment->proof)
                <a href="{{ asset('storage/'.$payment->proof) }}"
                   target="_blank"
                   class="text-blue-600 text-sm underline">
                   Bukti
                </a>
                @endif

            </div>

            <!-- 🔥 BAYAR -->
@if(
    $payment->status == 'menunggu_pembayaran'
    && in_array($payment->payment_type, ['tagihan', 'pelunasan'])
)

<a href="{{ route('customer.payment.page', $payment->order_id) }}"
   class="bg-blue-500 text-white px-3 py-1 rounded text-xs">
    {{ $payment->payment_type == 'pelunasan' ? 'Bayar Pelunasan' : 'Bayar Sekarang' }}
</a>

@endif


@if(
    $payment->status == 'menunggu_pembayaran'
    && $payment->payment_type == 'penalty'
    && $payment->order?->rental?->penalty
)

<a href="{{ route('penalty.pay.form', $payment->order->rental->penalty->id) }}"
   class="bg-red-500 text-white px-3 py-1 rounded text-xs">
    Bayar Denda
</a>

@endif
           @if(
    $payment->status == 'ditolak'
    && $payment->payment_type == 'penalty'
    && $payment->order?->rental?->penalty
)

<a href="{{ route('penalty.pay.form', $payment->order->rental->penalty->id) }}"
   class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
    Edit Denda
</a>

@endif



            <!-- EDIT -->
            @if($payment->status == 'ditolak' && in_array($payment->payment_type, ['dp','lunas','pelunasan']))
<a href="/customer/payment/{{ $payment->order_id }}"
   class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
   Edit
</a>
@endif

        </div>

    </div>

</div>

@empty

<div class="text-center text-gray-500 py-10">
    Belum ada data pembayaran
</div>

@endforelse

</div>

<script>
function toggleButton(input) {
    const form = input.closest('form');
    const button = form.querySelector('button');

    if (input.files.length > 0) {
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        button.disabled = true;
        button.classList.add('opacity-50', 'cursor-not-allowed');
    }
}
</script>

@endsection
