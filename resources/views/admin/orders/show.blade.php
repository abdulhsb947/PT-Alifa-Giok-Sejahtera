@extends('layouts.admin')

@section('content')

    <div class="p-6 max-w-6xl mx-auto">

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        @php
            $hasReviewLapangan = $order->admin_notes || $order->review_document;
            $canReviewLapangan = $order->status == 'menunggu_hasil_survey' && !$hasReviewLapangan;
            $canDecideOrder =
                $order->status == 'review_lapangan' || ($order->status == 'menunggu_verifikasi' && $hasReviewLapangan);
            $showReviewResult =
                $hasReviewLapangan && in_array($order->status, ['menunggu_verifikasi', 'review_lapangan']);
        @endphp

        <!-- HEADER -->
        <a href="/admin/orders" class="text-gray-500">← Kembali</a>

        <h1 class="text-2xl font-bold mt-2">
            {{ $order->order_code }}
        </h1>

        <p class="text-gray-500 mb-6">
            Dibuat pada {{ $order->created_at }}
        </p>

        <!-- ========================= -->
        <!-- GRID -->
        <!-- ========================= -->
        <div class="grid md:grid-cols-3 gap-6">

            <!-- ========================= -->
            <!-- LEFT -->
            <!-- ========================= -->
            <div class="md:col-span-2 space-y-6">

                <!-- DETAIL PESANAN -->
                <div class="bg-white p-6 rounded-xl shadow">

                    <h2 class="font-bold text-lg mb-4">Detail Pesanan</h2>

                    <div class="grid md:grid-cols-2 gap-6 text-sm">

                        <div class="flex gap-3">
                            <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">📍</div>
                            <div>
                                <p class="text-gray-500">Proyek</p>
                                <p class="font-medium">{{ $order->project_name }}</p>
                                <p class="text-gray-500">{{ $order->project_location }}</p>
                            </div>
                        </div>

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

                        <div class="w-full">
                            <p class="text-gray-500">Dokumen</p>

                            @php $document = $order->document ?? null; @endphp

                            @if ($document)
                                <a href="{{ asset('storage/' . $document) }}" target="_blank"
                                    class="text-blue-600 underline text-sm">
                                    👁 Lihat Dokumen
                                </a>
                            @else
                                <p class="font-medium text-gray-400">
                                    Belum ada dokumen
                                </p>
                            @endif

                        </div>
                    </div>


                    <!-- ===================== -->
                    <!-- CATATAN CUSTOMER -->
                    <!-- ===================== -->
                    @if ($order->notes)
                        <div class="bg-red-100 p-3 rounded mt-4">
                            <p class="text-sm font-semibold text-red-600 mb-1">
                                Catatan Pesanan
                            </p>
                            <p class="text-sm text-gray-700">
                                {{ $order->notes }}
                            </p>
                        </div>
                    @endif

                    <!-- PRODUK -->
                    <div class="mt-6">
                        <p class="font-medium mb-3">Produk</p>

                        @php $total = 0; @endphp

                        @foreach ($order->items as $item)
                            @php
                                $subtotal = $item->price * $item->quantity * $order->duration;
                                $total += $subtotal;
                            @endphp

                            <div class="flex justify-between bg-gray-50 p-3 rounded mb-2">
                                <p>{{ $item->product->name }} × {{ $item->quantity }}</p>
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

                <!-- KOMUNIKASI -->
                <div class="bg-white p-5 rounded-xl shadow">

                    <h2 class="font-bold mb-3">Komunikasi</h2>

                    <div class="h-64 overflow-y-auto border p-3 rounded mb-3 bg-gray-50">

                        @forelse($order->messages as $msg)
                            <div class="mb-3 {{ $msg->sender == 'admin' ? 'text-right' : 'text-left' }}">
                                <div
                                    class="inline-block max-w-xs px-3 py-2 rounded-lg 
                                {{ $msg->sender == 'admin' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
                                    {{ $msg->message }}
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
                            <input type="text" name="message" class="border w-full px-3 py-2 rounded"
                                placeholder="Ketik pesan..." required>

                            <button class="bg-blue-600 text-white px-4 rounded">
                                Kirim
                            </button>
                        </div>
                    </form>

                </div>

                @if ($canReviewLapangan)
                    <div class="bg-white p-5 rounded-xl shadow">
                        <h2 class="font-bold mb-3">Survey Lapangan</h2>

                        <form action="{{ route('admin.orders.review', $order->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">

                            <input type="file" name="review_document" accept="application/pdf,.pdf"
                                class="border p-2 w-full rounded mb-2">

                            <textarea name="admin_notes" class="border p-2 w-full rounded mb-2" placeholder="Hasil survey lapangan">{{ old('admin_notes') }}</textarea>

                            <button class="bg-yellow-500 text-white px-4 py-2 rounded">
                                Survey Lapangan
                            </button>
                        </form>
                    </div>
                @endif

                @if ($showReviewResult)
                    <div class="bg-yellow-100 p-5 rounded-xl shadow">
                        <h2 class="font-bold mb-3">Hasil Survey Lapangan</h2>

                        @if ($order->admin_notes)
                            <p class="text-sm font-semibold text-yellow-900 mb-1">
                                Catatan Admin Sebelumnya
                            </p>
                            <p class="text-sm text-yellow-800 mb-3">
                                {{ $order->admin_notes }}
                            </p>
                        @endif

                        @if ($order->review_document)
                            <a href="{{ asset('storage/' . $order->review_document) }}" target="_blank"
                                class="text-blue-600 underline text-sm">
                                Lihat Dokumen Survey Lapangan
                            </a>
                        @endif
                    </div>
                @endif

                @if ($canDecideOrder)
                    <div class="grid md:grid-cols-2 gap-4">
                        <form method="POST" action="/admin/orders/{{ $order->id }}/approve"
                            class="bg-white p-5 rounded-xl shadow">
                            @csrf
                            <textarea name="notes" placeholder="Catatan persetujuan (opsional)" class="border w-full p-2 rounded mb-2"></textarea>

                            <button class="bg-blue-600 text-white px-5 py-2 rounded">
                                Setujui
                            </button>
                        </form>

                        <form method="POST" action="/admin/orders/{{ $order->id }}/reject"
                            class="bg-white p-5 rounded-xl shadow">
                            @csrf
                            <textarea name="admin_notes" placeholder="Alasan penolakan" class="border w-full p-2 rounded mb-2"></textarea>

                            <button class="bg-red-500 text-white px-5 py-2 rounded">
                                Tolak
                            </button>
                        </form>
                    </div>
                @endif






                <!-- ========================= -->
                <!-- AGREEMENT -->
                <!-- ========================= -->
                @if ($order->agreement || $order->status == 'disetujui')

                    <div class="bg-white p-6 rounded-xl shadow">

                        <h3 class="font-bold mb-3">
                            📄 Dokumen Perjanjian
                        </h3>

                        @php
                            $agreement = $order->agreement;
                        @endphp

                        @if ($agreement)

                            <!-- STATUS -->
                            <div class="mb-4">

                                <span
                                    class="inline-block px-3 py-1 rounded text-white text-sm
                @if ($agreement->status == 'menunggu_persetujuan_pelanggan') bg-yellow-500
                @elseif($agreement->status == 'menunggu_penempatan_ttd')
                    bg-indigo-500
                @elseif($agreement->status == 'perjanjian_disetujui')
                    bg-green-500
                @elseif($agreement->status == 'perjanjian_ditolak')
                    bg-red-500 @endif
            ">

                                    @if ($agreement->status == 'menunggu_persetujuan_pelanggan')
                                        📄 Menunggu Persetujuan
                                    @elseif($agreement->status == 'menunggu_penempatan_ttd')
                                        Menunggu Penempatan Tanda Tangan
                                    @elseif($agreement->status == 'perjanjian_disetujui')
                                        ✍ Perjanjian Disetujui
                                    @elseif($agreement->status == 'perjanjian_ditolak')
                                        ❌ Perjanjian Ditolak
                                    @endif

                                </span>

                                <p class="mt-2 text-sm">
                                    <strong>Versi:</strong>
                                    V{{ $agreement->version }}
                                </p>

                            </div>

                            @if ($agreement->status == 'perjanjian_ditolak')
                                <div class="bg-red-50 border border-red-200 p-4 rounded mb-4">

                                    <h4 class="font-semibold text-red-600 mb-2">
                                        Alasan Penolakan Customer
                                    </h4>

                                    <p>{{ $agreement->customer_notes }}</p>

                                </div>
                            @endif

                            @if (!$agreement->file || $agreement->status == 'perjanjian_ditolak')
                                @if ($agreement->file)
                                    <p class="font-semibold mb-2">
                                        Dokumen Saat Ini
                                    </p>

                                    <iframe src="{{ asset('storage/' . $agreement->file) }}"
                                        class="w-full h-[400px] border rounded mb-3">
                                    </iframe>
                                @endif

                                <form method="POST" action="{{ route('admin.orders.uploadAgreement', $order->id) }}"
                                    enctype="multipart/form-data">

                                    @csrf

                                    <input type="file" name="file" required class="w-full border p-2 rounded mb-3">

                                    <textarea name="admin_notes" class="w-full border p-2 rounded mb-3" placeholder="Catatan admin">{{ $agreement->admin_notes }}</textarea>

                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">

                                        {{ $agreement->file ? 'Upload Ulang Perjanjian' : 'Upload Dokumen Perjanjian' }}

                                    </button>

                                </form>
                            @else
                                <iframe src="{{ asset('storage/' . $agreement->file) }}"
                                    class="w-full h-[400px] border rounded mb-3">
                                </iframe>
                            @endif

                            @if ($agreement->signature_file)
                                <div class="mb-5">

                                    <h4 class="font-semibold mb-2">
                                        Tanda Tangan Customer
                                    </h4>

                                    <img src="{{ asset('storage/' . $agreement->signature_file) }}"
                                        class="border rounded bg-white p-2 max-w-sm mb-3">

                                    <a href="{{ asset('storage/' . $agreement->signature_file) }}"
                                        download="Tanda_Tangan_{{ $order->order_code }}.png"
                                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">

                                        ⬇ Download PNG

                                    </a>

                                    @if ($agreement)
                                        <a
                                            href="{{ route('admin.agreement.place-signature', ['agreement' => $agreement->id]) }}"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded">
                                            Atur Posisi Tanda Tangan
                                            
                                        </a>
                                    @endif

                                </div>
                            @endif

                            



                            @if ($agreement->final_file)
                                <hr class="my-5">

                                <h4 class="font-semibold mb-2">
                                    Perjanjian Final Bertanda Tangan
                                </h4>

                                <iframe src="{{ asset('storage/' . $agreement->final_file) }}"
                                    class="w-full h-[400px] border rounded mb-3">
                                </iframe>

                                <a href="{{ asset('storage/' . $agreement->final_file) }}" target="_blank"
                                    class="text-green-600 underline">

                                    📄 Download Perjanjian Final

                                </a>
                            @endif
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">

                                <h4 class="font-semibold text-blue-700">
                                    📄 Belum Ada Perjanjian
                                </h4>

                                <p class="text-sm text-blue-600 mb-4">
                                    Upload dokumen perjanjian pertama untuk pesanan ini.
                                </p>

                                <form method="POST" action="{{ route('admin.orders.uploadAgreement', $order->id) }}"
                                    enctype="multipart/form-data">

                                    @csrf

                                    <input type="file" name="file" required class="w-full border p-2 rounded mb-3">

                                    <textarea name="admin_notes" class="w-full border p-2 rounded mb-3" placeholder="Catatan admin"></textarea>

                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">

                                        Upload Dokumen Perjanjian

                                    </button>

                                </form>

                            </div>

                        @endif

                    </div>

                @endif

                @if ($order->status == 'perjanjian_disetujui')
                    <a href="{{ route('admin.payments.create', [
                        'order_id' => $order->id,
                    ]) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded">

                        💰 Buat Tagihan

                    </a>
                @endif
            </div>





            <!-- ========================= -->
            <!-- RIGHT -->
            <!-- ========================= -->
            <div class="space-y-6">

                <!-- INFO CUSTOMER -->
                <div class="bg-white p-5 rounded-xl shadow">
                    <h2 class="font-bold mb-2">Informasi Pelanggan</h2>
                    <p>{{ $order->user->name }}</p>
                    <p class="text-gray-500 text-sm">{{ $order->user->phone }}</p>
                </div>

                <!-- STATUS -->
                <div class="bg-white p-5 rounded-xl shadow">
                    <h2 class="font-bold mb-3">Status</h2>

                    <!-- ===================== -->
                    <!-- STATUS ORDER -->
                    <!-- ===================== -->
                    <div class="mb-3">
                        <p class="text-sm text-gray-500 mb-1">Status Pesanan</p>

                        <span
                            class="px-3 py-1 rounded-full text-black text-sm
                        @if ($order->status == 'menunggu_hasil_survey') bg-orange-100
                        @elseif($order->status == 'menunggu_verifikasi')
                        bg-yellow-500
                        
                        @elseif($order->status == 'review_lapangan')
                        bg-blue-500
                        
                        @elseif($order->status == 'disetujui' || $order->status == 'perjanjian_disetujui')
                        bg-green-500
                        
                        @elseif($order->status == 'ditolak' || $order->status == 'perjanjian_ditolak')
                        bg-red-500
                        @elseif($order->status == 'menunggu_persetujuan_pelanggan')
                        bg-orange-500

                        @elseif($order->status == 'menunggu_penempatan_ttd')
                        bg-indigo-500 @endif
                        ">

                            @if ($order->status == 'menunggu_verifikasi')
                                ⏳ Menunggu Verifikasi
                            @elseif($order->status == 'menunggu_hasil_survey')
                                🕒 Menunggu Hasil Survey
                            @elseif($order->status == 'review_lapangan')
                                🔍 Review Lapangan
                            @elseif($order->status == 'disetujui')
                                ✔ Disetujui
                            @elseif($order->status == 'ditolak')
                                ✖ Ditolak
                            @elseif($order->status == 'menunggu_persetujuan_pelanggan')
                                📄 Menunggu Persetujuan
                            @elseif($order->status == 'menunggu_penempatan_ttd')
                                🖋 Menunggu Penempatan Tanda Tangan
                            @elseif($order->status == 'perjanjian_disetujui')
                                ✍ Perjanjian Disetujui
                            @elseif($order->status == 'perjanjian_ditolak')
                                ❌ Perjanjian Ditolak
                            @endif

                        </span>
                    </div>

                    <!-- ===================== -->
                    <!-- STATUS AGREEMENT -->
                    <!-- ===================== -->
                    @if ($order->agreement)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status Perjanjian</p>

                            <span
                                class="px-3 py-1 rounded-full text-white text-sm
            @if ($order->agreement->status == 'menunggu_persetujuan_pelanggan') bg-yellow-500
            @elseif($order->agreement->status == 'menunggu_penempatan_ttd') bg-indigo-500
            @elseif($order->agreement->status == 'perjanjian_disetujui') bg-green-500
            @elseif($order->agreement->status == 'perjanjian_ditolak') bg-red-500 @endif
        ">
                                {{ ucfirst(str_replace('_', ' ', $order->agreement->status)) }}
                            </span>
                        </div>
                    @endif



                    <div class="mb-3">
                        <p class="text-sm text-gray-500 mb-1">Status Pesanan</p>

                        @php
                            $payment = $order->payments->last();
                        @endphp

                        @if (!$payment)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded">
                                Belum Ada Tagihan
                            </span>
                        @elseif($payment->status == 'menunggu_pembayaran')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded">
                                Menunggu Pembayaran
                            </span>
                        @elseif($payment->status == 'menunggu_verifikasi')
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded">
                                Menunggu Verifikasi
                            </span>
                        @elseif($payment->status == 'disetujui')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded">
                                Pembayaran Disetujui
                            </span>
                        @elseif($payment->status == 'ditolak')
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded">
                                Pembayaran Ditolak
                            </span>
                        @endif

                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection
