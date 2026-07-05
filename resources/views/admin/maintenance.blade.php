@extends('layouts.admin')

@section('content')

<div>

    {{-- ========================= --}}
    {{-- HEADER --}}
    {{-- ========================= --}}
    <div class="flex justify-between items-start mb-6">

        <div>
            <h2 class="text-2xl font-bold">Manajemen Perawatan</h2>
            <p class="text-gray-500 text-sm">
                Pantau perbaikan dan riwayat perawatan scaffolding
            </p>
        </div>

        <button onclick="openModal()"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            + Tambah Perawatan
        </button>

    </div>

    {{-- ========================= --}}
    {{-- ALERT --}}
    {{-- ========================= --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- ========================= --}}
    {{-- RINGKASAN --}}
    {{-- ========================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-blue-100 p-3 rounded-lg">🔧</div>
            <div>
                <h3 class="text-xl font-bold">
                    {{ $maintenances->where('status','proses')->count() }}
                </h3>
                <p class="text-gray-500 text-sm">Sedang Diproses</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-green-100 p-3 rounded-lg">✅</div>
            <div>
                <h3 class="text-xl font-bold">
                    {{ $maintenances->where('status','selesai')->count() }}
                </h3>
                <p class="text-gray-500 text-sm">Selesai</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow flex items-center gap-3">
            <div class="bg-red-100 p-3 rounded-lg">💰</div>
            <div>
                <h3 class="text-xl font-bold">
                    Rp {{ number_format($maintenances->sum('price')) }}
                </h3>
                <p class="text-gray-500 text-sm">Total Biaya</p>
            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- LIST PERAWATAN --}}
    {{-- ========================= --}}
    @forelse($maintenances as $m)

    <div class="bg-white rounded-xl shadow p-5 mb-4">

        <div class="flex justify-between items-start">

            {{-- KIRI --}}
            <div class="flex-1">

                <h5 class="font-bold text-lg">
                    #{{ str_pad($m->id, 3, '0', STR_PAD_LEFT) }}

                    <span class="ml-2 px-2 py-1 text-xs rounded
                        {{ $m->status == 'proses'
                            ? 'bg-yellow-100 text-yellow-600'
                            : 'bg-green-100 text-green-600' }}">
                        {{ $m->status == 'proses' ? 'Diproses' : 'Selesai' }}
                    </span>
                </h5>

                <div class="grid grid-cols-2 md:grid-cols-6 gap-6 mt-4 text-sm">

                    <div>
                        <p class="text-gray-400">Produk</p>
                        <p class="font-semibold">{{ $m->product->name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400">Jumlah</p>
                        <p class="font-semibold">{{ $m->qty }} unit</p>
                    </div>

                    <div>
                        <p class="text-gray-400">Stok Tersedia</p>
                        <p class="font-semibold text-green-600">
                            {{ $m->product->available_stock }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Stok Perbaikan</p>
                        <p class="font-semibold text-red-500">
                            {{ $m->product->maintenance_stock }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Biaya</p>
                        <p class="font-semibold text-blue-600">
                            Rp {{ number_format($m->price) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Catatan</p>
                        <p class="font-semibold">
                            {{ $m->notes ?? '-' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- KANAN --}}
            <div class="ml-6">

                @if($m->status == 'proses')
                <form action="{{ route('admin.maintenance.selesai', $m->id) }}" method="POST">
                    @csrf
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        Tandai Selesai
                    </button>
                </form>
                @else
                <span class="bg-green-100 text-green-600 px-3 py-1 rounded text-sm">
                    Selesai
                </span>
                @endif

            </div>

        </div>

    </div>

    @empty
    <div class="bg-white p-6 rounded-xl shadow text-center text-gray-500">
        Belum ada data perawatan
    </div>
    @endforelse

</div>

{{-- ========================= --}}
{{-- MODAL --}}
{{-- ========================= --}}
<div id="modalMaintenance"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-lg">

        <h3 class="text-lg font-bold mb-4">Tambah Data Perawatan</h3>

        <form method="POST" action="{{ route('admin.maintenance.store') }}">
            @csrf

            <div class="mb-3">
                <label class="text-sm text-gray-500">Produk</label>
                <select name="product_id" class="w-full border p-2 rounded">
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->name }} (Tersedia: {{ $p->available_stock }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="text-sm text-gray-500">Jumlah</label>
                <input type="number" name="qty" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label class="text-sm text-gray-500">Biaya</label>
                <input type="number" name="price" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="text-sm text-gray-500">Catatan</label>
                <textarea name="notes" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="flex justify-end gap-2">

                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 bg-gray-300 rounded">
                    Batal
                </button>

                <button class="px-4 py-2 bg-blue-500 text-white rounded">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

{{-- ========================= --}}
{{-- SCRIPT --}}
{{-- ========================= --}}
<script>
function openModal() {
    document.getElementById('modalMaintenance').classList.remove('hidden');
    document.getElementById('modalMaintenance').classList.add('flex');
}

function closeModal() {
    document.getElementById('modalMaintenance').classList.add('hidden');
    document.getElementById('modalMaintenance').classList.remove('flex');
}
</script>

@endsection