<!DOCTYPE html>

@php
$notifications = \App\Models\AdminNotification::latest()
    ->take(10)
    ->get();

$notifCount = \App\Models\AdminNotification::where('is_read', 0)
    ->count();
@endphp

<html>
<head>
    <title>Admin - ScaffoldPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col h-screen">

        <!-- LOGO -->
        <div class="p-4 text-xl font-bold border-b border-slate-700">
            PT ALIFA GIOK SEJAHTERA<span class="text-blue-400"></span>
        </div>

        <!-- MENU -->
        <nav class="flex-1 p-4 space-y-2">

            <a href="/admin/"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Dashboard
            </a>

            <a href="/admin/orders"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin/orders*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Pesanan
            </a>

            <a href="/admin/products"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin/products*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Produk & Stok
            </a>

            <a href="/admin/rentals"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin/rentals*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Penyewaan
            </a>

            <a href="/admin/payments"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin/payments*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Pembayaran
            </a>

             <div x-data="{
    open: {{ request()->is('admin/maintenance*') || request()->is('admin/lost-products*') ? 'true' : 'false' }}
}">

    <button
        @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-700 transition">

        <div class="flex items-center gap-3 flex-1">

            <span class="text-left">
                Laporan Kerusakan & Kehilangan
            </span>
        </div>

    </button>

    <div
        x-show="open"
        x-transition
        class="ml-8 mt-2 space-y-1">

        <a href="/admin/maintenance"
            class="block px-3 py-2 rounded
            {{ request()->is('admin/maintenance*')
                ? 'bg-blue-600 text-white'
                : 'hover:bg-slate-700' }}">
            Perawatan Produk
        </a>

        <a href="/admin/lost-products"
            class="block px-3 py-2 rounded
            {{ request()->is('admin/lost-products*')
                ? 'bg-blue-600 text-white'
                : 'hover:bg-slate-700' }}">
            Produk Hilang
        </a>

    </div>

</div>

            <a href="/admin/offline"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin/offline*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Pemesanan & Penyewaan
            </a>

            <a href="/admin/reports"
               class="block px-3 py-2 rounded 
               {{ request()->is('admin/reports*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                Laporan
            </a>

        </nav>

        <!-- BOTTOM MENU -->
        <div class="p-4 border-t border-slate-700 space-y-2">

            <a href="/admin/settings"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-700">
                ⚙️ Pengaturan
            </a>

            <!-- 🔥 LOGOUT BUTTON -->
            <button onclick="openLogoutModal()"
                class="flex items-center gap-2 px-3 py-2 w-full text-left hover:bg-slate-700 rounded">
                🚪 Keluar
            </button>

        </div>

    </aside>

   <!-- CONTENT AREA -->
<div class="flex-1 overflow-x-hidden">

    <!-- TOP NAVBAR -->
    <header class="bg-white shadow border-b">

    <div class="px-10 py-4 flex items-center justify-between">

        <!-- KIRI -->
        <div>

            @php
                $title = 'Dashboard';

                if(request()->is('admin/orders*'))
                    $title = 'Manajemen Pesanan';

                elseif(request()->is('admin/products*'))
                    $title = 'Produk & Stok';

                elseif(request()->is('admin/rentals*'))
                    $title = 'Penyewaan';

                elseif(request()->is('admin/payments*'))
                    $title = 'Pembayaran';

                elseif(request()->is('admin/maintenance*'))
                    $title = 'Perawatan Produk';

                    elseif(request()->is('admin/lost-products*'))
                    $title = 'Produk Hilang';

                elseif(request()->is('admin/offline*'))
                    $title = 'Pemesanan & Penyewaan';

                elseif(request()->is('admin/reports*'))
                    $title = 'Laporan';
            @endphp

            <h1 class="text-2xl font-bold text-gray-800">
                {{ $title }}
            </h1>

            <p class="text-sm text-gray-500">
                Selamat datang, {{ auth()->user()->name }}
            </p>

        </div>

        <!-- KANAN -->
        <div class="flex items-center gap-4">

            <div class="relative">

    <button onclick="toggleAdminNotif()"
        class="text-xl relative">

        🔔

        @if($notifCount > 0)
<span id="adminNotifBadge"
      class="absolute -top-1 -right-2
             bg-blue-600 text-white
             text-xs px-1.5 rounded-full">
    {{ $notifCount }}
</span>
@endif

    </button>

    <div id="adminNotifDropdown"
         class="hidden absolute right-0 mt-2
                w-96 bg-white rounded-lg
                shadow-lg border z-50">

        <div class="p-3 border-b font-semibold">
            Notifikasi
        </div>

        @forelse($notifications as $notif)

            <div class="notif-item flex justify-between items-start p-3 border-b hover:bg-gray-50">

    <a href="{{ route('admin.notifications.read',$notif->id) }}"
       class="flex-1">

        <div class="{{ $notif->is_read ? '' : 'font-semibold' }}">
            {{ $notif->title }}
        </div>

        <div class="text-sm text-gray-500">
            {{ $notif->message }}
        </div>

    </a>

    <button
        onclick="deleteAdminNotif({{ $notif->id }}, this)"
        class="ml-2 rounded px-2 py-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-blue-600">
        Hapus
    </button>

</div>

        @empty

            <div class="p-3 text-gray-500">
                Tidak ada notifikasi
            </div>

        @endforelse

    </div>

</div>

            <div class="flex items-center gap-2">

                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center">
                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>

                <div>
                    <p class="font-semibold">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-xs text-gray-500">
                        Admin
                    </p>
                </div>

            </div>

        </div>

    </div>

</header>


    <!-- CONTENT -->
    <div class="px-6 py-6">
    @yield('content')
</div>

</div>



<!-- 🔥 LOGOUT MODAL -->
<div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-full max-w-sm text-center shadow-lg">

        <h2 class="text-lg font-bold mb-2">Konfirmasi Keluar</h2>
        <p class="text-gray-500 mb-6">
            Apakah Anda yakin ingin keluar dari sistem?
        </p>

        <div class="flex justify-center gap-3">

            <!-- NO -->
            <button onclick="closeLogoutModal()"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Tidak
            </button>

            <!-- YES -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Iya
                </button>
            </form>

        </div>

    </div>

</div>

<!-- 🔥 SCRIPT -->
<script>
function openLogoutModal() {
    document.getElementById('logoutModal').classList.remove('hidden');
    document.getElementById('logoutModal').classList.add('flex');
}

function closeLogoutModal() {
    document.getElementById('logoutModal').classList.add('hidden');
    document.getElementById('logoutModal').classList.remove('flex');
}
</script>

<script>
function toggleAdminNotif()
{
    document
        .getElementById('adminNotifDropdown')
        .classList.toggle('hidden');
}
</script>

<script>

function deleteAdminNotif(id, btn)
{
    fetch('/admin/notifications/' + id, {

        method: 'DELETE',

        headers: {
            'X-CSRF-TOKEN':
                document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,

            'Accept': 'application/json'
        }

    })

    .then(res => res.json())

    .then(data => {

        btn.closest('.notif-item').remove();

        let badge = document.querySelector(
            '#adminNotifBadge'
        );

        if(badge)
        {
            let count = parseInt(
                badge.innerText
            );

            if(count > 1)
            {
                badge.innerText = count - 1;
            }
            else
            {
                badge.remove();
            }
        }

    });

}

</script>

</body>
</html>
