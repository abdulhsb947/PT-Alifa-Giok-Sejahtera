@extends('layouts.customer')

@section('content')



<div class="p-6 max-w-6xl mx-auto">

    <!-- HEADER -->
    <h1 class="text-2xl font-bold mb-1">
        {{ $order->order_code }}

        <span class="text-sm px-2 py-1 rounded ml-2
        @if(
    $order->status == 'disetujui' ||
    $order->status == 'perjanjian_disetujui'
)

bg-green-100 text-green-600

@elseif(
    $order->status == 'ditolak' ||
    $order->status == 'perjanjian_ditolak'
)

bg-red-100 text-red-600

@elseif(
    $order->status == 'review_lapangan'
)

bg-blue-100 text-blue-600

@elseif(
    $order->status == 'menunggu_persetujuan_pelanggan'
)

bg-yellow-100 text-yellow-600

@else

bg-gray-100 text-gray-600

@endif">
            {{ ucfirst(str_replace('_',' ',$order->status)) }}
        </span>
    </h1>

    <p class="text-gray-500 mb-6">
        Dibuat pada {{ $order->created_at }}
    </p>

    <!-- CARD -->
    <div class="bg-white p-6 rounded-xl shadow">

        <!-- DETAIL -->
        <h2 class="font-bold text-lg mb-4">Detail Pesanan</h2>

        <div class="grid md:grid-cols-2 gap-6 text-sm">

            <!-- PROYEK -->
            <div class="flex gap-3">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">📍</div>
                <div>
                    <p class="text-gray-500">Proyek</p>
                    <p class="font-medium">{{ $order->project_name }}</p>
                    <p class="text-gray-500">{{ $order->project_location }}</p>
                </div>
            </div>

            <!-- JADWAL -->
            <div class="flex gap-3">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">📅</div>
                <div>
                    <p class="text-gray-500">Jadwal</p>
                    <p class="font-medium">Mulai: {{ $order->start_date }}</p>
                    <p class="text-gray-500">
                        Durasi: {{ $order->duration }} {{ $order->duration_unit }}
                    </p>
                </div>
            </div>

        </div>

        <!-- DOKUMEN -->
        <div class="flex gap-3 mt-4 items-start">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">📄</div>

            <div>
                <p class="text-gray-500">Dokumen</p>

                @if($order->document)
                <a href="{{ asset('storage/'.$order->document) }}"
                    target="_blank"
                    class="text-blue-600 underline text-sm">
                    👁 Lihat Dokumen
                </a>
                @else
                <p class="text-gray-400">Belum ada dokumen</p>
                @endif
            </div>
        </div>

        <!-- PRODUK -->
        <div class="mt-6">
            <p class="font-medium mb-3">Produk</p>

            @php $total = 0; @endphp

            @foreach($order->items as $item)
            @php
            $subtotal = $item->price * $item->quantity * $order->duration;
            $total += $subtotal;
            @endphp

            <div class="flex justify-between bg-gray-50 p-3 rounded mb-2">
                <p>{{ $item->product->name }} × {{ $item->quantity }} unit</p>
                <p class="text-blue-600 font-semibold">
                    Rp {{ number_format($subtotal) }}
                </p>
            </div>
            @endforeach

            <div class="flex justify-between mt-3 border-t pt-3 font-bold">
                <span>Total</span>
                <span class="text-blue-600">
                    Rp {{ number_format($total) }}
                </span>
            </div>
        </div>

    </div>

    <!-- HASIL REVIEW LAPANGAN -->
    @if(
    $order->status == 'review_lapangan'
    && ($order->admin_notes || $order->review_document)
    )

    <div class="bg-yellow-100 p-4 rounded mt-4">

        <h4 class="font-bold mb-2">
            🔍 Hasil Review Lapangan
        </h4>

        @if($order->admin_notes)
        <div class="mb-3 text-yellow-700">
            {{ $order->admin_notes }}
        </div>
        @endif

        @if($order->review_document)
        <a
            href="{{ asset('storage/'.$order->review_document) }}"
            target="_blank"
            class="text-blue-600 underline">
            📄 Lihat Dokumen Survey Lapangan
        </a>
        @endif

    </div>

    @endif

    <!-- EDIT JIKA DITOLAK -->
    @if($order->status == 'review_lapangan'
    || $order->status == 'ditolak')
    <div class="mt-4">
        <a href="{{ route('customer.orders.edit', $order->id) }}"
            class="bg-yellow-500 text-white px-5 py-2 rounded">
            Edit Pesanan
        </a>
    </div>
    @endif

    <!-- CHAT -->
    <div class="bg-white p-6 rounded-xl shadow mt-6">

        <h2 class="font-bold mb-3">Komunikasi</h2>

        <div class="h-64 overflow-y-auto border p-3 rounded mb-3 bg-gray-50">

            @forelse($order->messages as $msg)
            <div class="mb-3 {{ $msg->sender == 'customer' ? 'text-right' : 'text-left' }}">

                <div class="inline-block max-w-xs px-3 py-2 rounded-lg 
                {{ $msg->sender == 'customer' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">

                    <div class="text-xs font-semibold mb-1">
                        {{ $msg->sender == 'admin' ? 'Admin' : 'Anda' }}
                    </div>

                    <div>{{ $msg->message }}</div>

                    <div class="text-[10px] mt-1 text-right opacity-70">
                        {{ $msg->created_at->format('H:i') }}
                    </div>

                </div>

            </div>
            @empty
            <p class="text-gray-400 text-center text-sm">
                Belum ada pesan
            </p>
            @endforelse

        </div>

        <form method="POST" action="{{ route('chat.send', $order->id) }}">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="message"
                    class="border w-full px-3 py-2 rounded"
                    placeholder="Ketik pesan..." required>

                <button class="bg-blue-600 text-white px-4 rounded">
                    Kirim
                </button>
            </div>
        </form>

    </div>


    <!-- AGREEMENT -->
    @if($order->agreement)

    <div class="bg-white p-6 rounded-xl shadow mt-6">

        <h3 class="font-bold mb-3">
            📄 Dokumen Persetujuan
        </h3>

        <!-- PDF PERJANJIAN -->
        <iframe
            src="{{ asset('storage/'.$order->agreement->file) }}"
            class="w-full h-[400px] border rounded mb-4">
        </iframe>

        <div class="flex gap-4 mb-3">

            <a href="{{ asset('storage/'.$order->agreement->file) }}"
                target="_blank"
                class="text-blue-600 underline">
                👁 Lihat
            </a>

            <a href="{{ asset('storage/'.$order->agreement->file) }}"
                download
                class="text-green-600 underline">
                ⬇ Unduh
            </a>

        </div>

        <!-- INFO -->
        <div class="bg-gray-50 p-3 rounded mb-4">

            <p>
                <strong>Versi:</strong>
                V{{ $order->agreement->version }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ ucfirst(str_replace('_',' ',
            $order->agreement->status)) }}
            </p>

            <p class="mt-2">
                <strong>Catatan Admin:</strong>
                {{ $order->agreement->admin_notes ?? '-' }}
            </p>

        </div>

        {{-- =========================
        MENUNGGU PERSETUJUAN
    ========================== --}}
        @if(
        $order->agreement->status ==
        'menunggu_persetujuan_pelanggan'
        )

        <!-- APPROVE -->
        <form
            method="POST"
            action="/customer/orders/{{ $order->id }}/approve-agreement">

            @csrf

            <h4 class="font-semibold mb-2">
                ✍ Tanda Tangan Digital
            </h4>

            <canvas
                id="signature-pad"
                width="600"
                height="200"
                class="border rounded w-full mb-3">
            </canvas>

            <input
                type="hidden"
                name="signature"
                id="signature">

            <div class="flex gap-2 mb-3">

                <button
                    type="button"
                    id="clear-signature"
                    class="bg-gray-500 text-white px-4 py-2 rounded">

                    Hapus

                </button>

            </div>

            <textarea
                name="customer_notes"
                class="w-full border p-2 mb-2 rounded"
                placeholder="Catatan tambahan (opsional)"></textarea>

            <button
                class="bg-blue-600 text-white px-4 py-2 rounded w-full">

                Setujui & Tanda Tangani

            </button>

        </form>

        <!-- REJECT -->
        <form
            method="POST"
            action="/customer/orders/{{ $order->id }}/reject-agreement"
            class="mt-4">

            @csrf

            <label class="text-sm text-red-500 font-medium">
                Alasan Penolakan *
            </label>

            <textarea
                id="rejectNotes"
                name="notes"
                class="w-full border p-2 mb-2 rounded"
                placeholder="Tuliskan alasan penolakan..."
                required
                oninput="toggleRejectButton()"></textarea>

            <p
                id="errorText"
                class="text-red-500 text-xs mb-2 hidden">

                ⚠️ Alasan wajib diisi

            </p>

            <button
                id="rejectBtn"
                disabled
                class="bg-red-300 text-white px-4 py-2 rounded w-full cursor-not-allowed">

                Tolak Perjanjian

            </button>

        </form>

        @endif

    </div>

    @else

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">

        <h4 class="font-semibold text-blue-700">
            📄 Menunggu Perjanjian
        </h4>

        <p class="text-sm text-blue-600 mt-1">
            Admin belum mengunggah dokumen perjanjian.
        </p>

    </div>



    @endif

    @if(
    $order->agreement &&
    $order->agreement->status == 'perjanjian_ditolak'
    )

    <div class="bg-red-50 border border-red-200 p-4 rounded mt-4">

        <h4 class="font-bold text-red-600 mb-2">
            ❌ Perjanjian Ditolak
        </h4>

        <p>
            {{ $order->agreement->customer_notes }}
        </p>

        <p class="text-sm text-gray-500 mt-2">
            Menunggu admin mengunggah revisi perjanjian.
        </p>

    </div>

    @endif


    @if(
    $order->agreement &&
    $order->agreement->status == 'perjanjian_disetujui'
    )

    <div class="bg-green-50 border border-green-200 p-4 rounded mt-4">

        <h4 class="font-bold text-green-600 mb-2">
            ✅ Perjanjian Disetujui
        </h4>

        <p class="mb-3">
            Perjanjian telah ditandatangani.
        </p>

        @if($order->agreement->final_file)

        <iframe
            src="{{ asset('storage/'.$order->agreement->final_file) }}"
            class="w-full h-[500px] border rounded mb-3">
        </iframe>

        <a
            href="{{ asset('storage/'.$order->agreement->final_file) }}"
            target="_blank"
            class="text-blue-600 underline">

            📄 Download Perjanjian Final

        </a>

        @endif

    </div>

    @endif

    <!-- PAYMENT -->

     @php
$tagihan = $order->payments
    ->where('status', 'menunggu_pembayaran')
    ->first();

$hasCustomerPayment = $order->payments
    ->whereIn('payment_type', [
        'dp',
        'lunas',
        'pelunasan'
    ])
    ->count() > 0;
@endphp

    @if(
    $order->agreement &&
    $order->agreement->status == 'perjanjian_disetujui'
)

    @if($tagihan && !$hasCustomerPayment)

    <a
        href="{{ route('customer.payment.page', $order->id) }}"
        class="bg-blue-600 text-white px-4 py-2 rounded mt-6 inline-block">

        💳 Lanjut ke Pembayaran

    </a>

@elseif(!$tagihan)

    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
        ⏳ Menunggu admin membuat tagihan.
    </div>

@else

    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded">
        ✅ Pembayaran sudah dikirim. Silakan tunggu verifikasi admin.
    </div>

@endif

@endif

</div>

<!-- SCRIPT -->
<script>
    function toggleRejectButton() {
        const notes = document.getElementById('rejectNotes').value.trim();
        const button = document.getElementById('rejectBtn');
        const errorText = document.getElementById('errorText');

        if (notes.length > 0) {
            button.disabled = false;
            button.classList.remove('bg-red-300', 'cursor-not-allowed');
            button.classList.add('bg-red-500');
            errorText.classList.add('hidden');
        } else {
            button.disabled = true;
            button.classList.add('bg-red-300', 'cursor-not-allowed');
            button.classList.remove('bg-red-500');
            errorText.classList.remove('hidden');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    const canvas = document.getElementById('signature-pad');

    if (canvas) {

        const signaturePad = new SignaturePad(canvas);

        document.querySelector(
            'form[action*="approve-agreement"]'
        ).addEventListener('submit', function(e) {

            if (signaturePad.isEmpty()) {

                alert('Silakan tanda tangan terlebih dahulu');

                e.preventDefault();

                return;
            }

            document.getElementById('signature').value =
                signaturePad.toDataURL();
        });

        document.getElementById(
            'clear-signature'
        ).addEventListener('click', function() {

            signaturePad.clear();

        });
    }
</script>

@endsection