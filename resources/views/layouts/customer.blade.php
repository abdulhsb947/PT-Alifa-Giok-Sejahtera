<!DOCTYPE html>


@php
$notifications = \App\Models\Notification::where('user_id', auth()->id())
->latest()
->take(5)
->get();

$notifCount = \App\Models\Notification::where('user_id', auth()->id())
->where('is_read', false)
->count();
@endphp

<html>

<head>
    <title>Customer - ScaffoldPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .gradient-hero {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
        }

        .text-gradient {
            background: linear-gradient(to right, #93c5fd, #60a5fa);

            background-clip: text;
            /* standar */
            -webkit-background-clip: text;

            color: transparent;
            /* standar */
            -webkit-text-fill-color: transparent;
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- NAVBAR -->
    <nav class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-6 py-3 flex items-center justify-between">

            <!-- LOGO -->
            <div class="flex items-center gap-2">

                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/image/PT-logo.png') }}"
                        class="w-full h-full object-cover">
                </div>

                <div class="font-bold text-xl text-blue-600 tracking-wide">
                    ALIFA GIOK SEJAHTERA
                </div>

            </div>

            <!-- MENU -->
            <div class="hidden md:flex gap-8 text-sm font-medium">

                <a href="/customer"
                    class="{{ request()->is('customer') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                    Beranda
                </a>

                <a href="/customer/about"
                    class="{{ request()->is('customer/about') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                    Tentang
                </a>

                <a href="/customer/products"
                    class="{{ request()->is('customer/products') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                    Produk
                </a>

                <a href="/customer/contact"
                    class="{{ request()->is('customer/contact') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                    Kontak
                </a>

            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-4">



                <!-- NOTIF -->
                <div class="relative">

                    <!-- 🔔 BUTTON -->
                    <button onclick="toggleNotif()"
                        class="text-xl cursor-pointer hover:scale-110 transition relative">

                        🔔

                        @if($notifCount > 0)
                        <span id="customerNotifBadge" class="absolute -top-1 -right-1 bg-blue-600 text-white text-xs px-1 rounded-full">
                            {{ $notifCount }}
                        </span>
                        @endif

                    </button>

                    <!-- 🔽 DROPDOWN -->
                    <div id="notifDropdown"
                        class="hidden absolute right-0 top-full mt-2 w-80 bg-white shadow-lg rounded-xl border z-50">

                        <div class="p-3 border-b font-semibold text-sm">
                            Notifikasi
                        </div>

                        <div class="max-h-80 overflow-y-auto">

                            @forelse($notifications as $notif)

                            <div class="notif-item flex items-start justify-between gap-2 p-3 border-b 
                hover:bg-gray-50
                {{ $notif->is_read ? 'bg-gray-100' : 'bg-white font-semibold' }}">

                                <!-- KLIK -->
                                <a href="{{ route('notifications.read', $notif->id) }}" class="flex-1 pr-2">
                                    <p class="text-sm">{{ $notif->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $notif->message }}</p>
                                </a>

                                <!-- HAPUS -->
                                <button type="button"
                                    onclick="deleteNotif({{ $notif->id }}, this)"
                                    class="rounded px-2 py-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-blue-600">
                                    Hapus
                                </button>

                            </div>

                            @empty
                            <div class="p-3 text-center text-gray-400 text-sm">
                                Tidak ada notifikasi
                            </div>
                            @endforelse

                        </div>

                    </div>

                </div>

                <!-- PESANAN -->
                <a href="/customer/orders"
                    class="flex items-center gap-2 border border-blue-500 text-blue-500 px-4 py-1.5 rounded-lg text-sm hover:bg-blue-50 relative transition">

                    🛒 <span>Pesanan Saya</span>

                    <!-- BADGE -->
                    @if(($orderCount ?? 0) > 0)
                    <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs px-1.5 rounded-full">
                        {{ $orderCount }}
                    </span>
                    @endif

                </a>

                <!-- USER -->
                <div class="relative">

                    <button onclick="toggleDropdown()"
                        class="flex items-center gap-2 cursor-pointer hover:opacity-80">

                        <!-- FOTO / AVATAR -->
                        @if(auth()->user()->photo)
                        <img src="{{ asset('storage/profile/'.auth()->user()->photo) }}"
                            class="w-8 h-8 rounded-full object-cover">
                        @else
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                        </div>
                        @endif

                        <!-- NAMA 1 KATA -->
                        <span class="text-sm font-medium">
                            {{ explode(' ', auth()->user()->name)[0] }}
                        </span>

                        🔽
                    </button>

                    <!-- DROPDOWN -->
                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-3 w-56 bg-white shadow-lg rounded-xl z-50">

                        <div class="p-4 border-b">
                            <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="text-sm">

                            <a href="/customer/orders" class="block px-4 py-2 hover:bg-gray-100">
                                Pesanan Saya
                            </a>

                            <a href="/customer/rentals" class="block px-4 py-2 hover:bg-gray-100">
                                Penyewaan
                            </a>

                            <a href="/customer/payments" class="block px-4 py-2 hover:bg-gray-100">
                                Pembayaran
                            </a>

                            <a href="/customer/settings" class="block px-4 py-2 hover:bg-gray-100">
                                Pengaturan
                            </a>

                            <!-- LOGOUT -->
                            <button onclick="openLogoutModal()"
                                class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Keluar
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </nav>

    <!-- CONTENT -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    <!-- 🔥 LOGOUT MODAL -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-xl p-6 w-full max-w-sm text-center shadow-lg">

            <h2 class="text-lg font-bold mb-2">Konfirmasi Keluar</h2>
            <p class="text-gray-500 mb-6">
                Apakah Anda yakin ingin keluar?
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

    <!-- SCRIPT -->
    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.getElementById('dropdownMenu').classList.add('hidden');
            }
        });

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
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>

    <script>
        function toggleNotif() {
            document.getElementById('notifDropdown').classList.toggle('hidden');
        }
    </script>
    <script>
        function deleteNotif(id, el) {

            fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {

                    const item = el.closest('.notif-item');

                    if (item) {
                        item.remove();
                    }

                    // update badge
                    let badge = document.querySelector('#customerNotifBadge');

                    if (badge) {
                        let count = parseInt(badge.innerText);
                        if (count > 1) {
                            badge.innerText = count - 1;
                        } else {
                            badge.remove();
                        }
                    }

                })
                .catch(err => console.log(err));
        }
    </script>

    @include('components.footer')

</body>

</html>
