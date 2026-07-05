<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direktur - PT Alifa Giok Sejahtera</title>


    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js"></script>



</head>

<body class="bg-slate-100">

    <div class="flex min-h-screen">


        <!-- SIDEBAR -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col">

            <!-- LOGO -->
            <div class="p-5 border-b border-slate-700">

                <h1 class="font-bold text-lg">
                    PT Alifa Giok Sejahtera
                </h1>

                <p class="text-xs text-slate-400">
                    Direktur Panel
                </p>

            </div>

            <!-- USER -->
           

            <!-- MENU -->
            <nav class="flex-1 p-4 space-y-2">

                <a href="{{ url('/direktur/') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/dashboard*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>📊</span>
                    Dashboard

                </a>

                <a href="{{ url('/direktur/rental') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/rental*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>📦</span>
                    Laporan Penyewaan

                </a>

                <a href="{{ url('/direktur/orders') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/orders*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>📦</span>
                    Laporan Pemesanan

                </a>

                <a href="{{ url('/direktur/payments') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/payments*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>📦</span>
                    Laporan Pembayaran

                </a>

                <a href="{{ url('/direktur/stock') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/stock*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>📋</span>
                    Laporan Stok

                </a>

                <div>

                    <div <div x-data="{
                        open: {{ request()->is('direktur/maintenance*') || request()->is('direktur/lost-products*') ? 'true' : 'false' }}
                    }">

                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-800 transition">

                            <div class="flex items-center gap-3 flex-1">
                                <span>🛠️</span>
                                <span class="text-left whitespace-normal">
                                    Laporan Kerusakan & Kehilangan
                                </span>
                            </div>
                        </button>

                        <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">

                            <a href="/direktur/maintenance"
                                class="block px-3 py-2 rounded
            {{ request()->is('direktur/maintenance*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">
                                Perawatan Produk
                            </a>

                            <a href="/direktur/lost-products"
                                class="block px-3 py-2 rounded
            {{ request()->is('direktur/lost-products*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">
                                Produk Hilang
                            </a>

                        </div>

                    </div>

                </div>

                <a href="{{ url('/direktur/security') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/security*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>🔐</span>
                    Log Keamanan

                </a>

                <a href="{{ url('/direktur/users') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition
            {{ request()->is('direktur/users*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800' }}">

                    <span>�</span>
                    Manajemen Pengguna

                </a>

            </nav>

            <!-- FOOTER -->
            <div class="border-t border-slate-700 p-4 space-y-2">

                <a href="{{ url('/direktur/settings') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">

                    <span>⚙️</span>
                    Pengaturan

                </a>

                <button onclick="openLogoutModal()"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 w-full text-left">

                    <span>🚪</span>
                    Keluar

                </button>

            </div>

        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col">

            <!-- HEADER -->
            <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">

                <div>

                    <h2 class="text-xl font-bold">
                        @yield('title', 'Dashboard Direktur')
                    </h2>

                    <p class="text-sm text-gray-500">
                        @yield('subtitle', 'Sistem Informasi Penyewaan Scaffolding')
                    </p>

                </div>

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="text-right">

                        <div class="font-semibold">
                            {{ auth()->user()->name ?? 'Direktur' }}
                        </div>

                        <div class="text-xs text-gray-500">
                            Direktur
                        </div>

                    </div>

                    

                </div>

            </header>

            <!-- CONTENT -->
            <main class="flex-1 p-6">

                @if (session('success'))
                    <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')

            </main>

        </div>


    </div>

    <!-- LOGOUT MODAL -->

    <div id="logoutModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">


        <div class="bg-white rounded-xl p-6 w-full max-w-md text-center  shadow-xl">

            <h3 class="text-xl font-bold mb-2">
                Konfirmasi Keluar
            </h3>

            <p class="text-gray-500 mb-6">
                Apakah Anda yakin ingin keluar dari sistem?
            </p>

            <div class="flex justify-center gap-3">

                <button onclick="closeLogoutModal()" class="px-4 py-2 border rounded-lg">

                    Tidak

                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                        Iya

                    </button>

                </form>

            </div>

        </div>


    </div>

    <script>
        function openLogoutModal() {
            document.getElementById('logoutModal')
                .classList.remove('hidden');

            document.getElementById('logoutModal')
                .classList.add('flex');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal')
                .classList.add('hidden');

            document.getElementById('logoutModal')
                .classList.remove('flex');
        }
    </script>

</body>

</html>
