<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('storage/image/PT-logo.png') }}?v=2">
    <title>PT ALIFA GIOK SEJAHTERA</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icon -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- CUSTOM CSS -->
    <style>
        .gradient-hero {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
        }

        .text-gradient {
    background: linear-gradient(to right, #93c5fd, #60a5fa);
    
    background-clip: text; /* standar */
    -webkit-background-clip: text;

    color: transparent; /* standar */
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

@if(!request()->is('login') && !request()->is('register') && !request()->is('forgot-password'))

<!-- NAVBAR -->
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">

        <!-- LOGO -->
        <div class="flex items-center gap-2">

    <!-- LOGO -->
    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
        <img src="{{ asset('storage/image/PT-logo.png') }}" 
             alt="Logo"
             class="w-full h-full object-cover">
    </div>

    <!-- TEXT -->
    <div class="font-bold text-xl text-blue-600 tracking-wide">
        ALIFA GIOK SEJAHTERA
    </div>

</div>
        <!-- MENU CENTER -->
        <div class="hidden md:flex gap-8 font-medium">

            <a href="/"
               class="{{ request()->path() == '/' ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-500' }}">
               Beranda
            </a>

            <a href="/tentang"
               class="{{ request()->is('tentang') ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-500' }}">
               Tentang
            </a>

            <a href="/products"
               class="{{ request()->is('products') ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-500' }}">
               Produk
            </a>

            <a href="/kontak"
               class="{{ request()->is('kontak') ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-500' }}">
               Kontak
            </a>

        </div>

        <!-- RIGHT -->
        <div class="flex items-center gap-4">

        

            <!-- LOGIN -->
            <a href="/login" class="text-gray-700 hover:text-blue-500">
                Masuk
            </a>

            <!-- REGISTER -->
            <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Daftar
            </a>

        </div>

    </div>

    <!-- MOBILE MENU -->
    <div class="md:hidden px-6 pb-4">
        <div class="flex flex-col gap-2 font-medium">

            <a href="/"
               class="{{ request()->path() == '/' ? 'text-blue-600 font-semibold' : 'hover:text-blue-500' }}">
               Beranda
            </a>

            <a href="/tentang"
               class="{{ request()->is('tentang') ? 'text-blue-600 font-semibold' : 'hover:text-blue-500' }}">
               Tentang
            </a>

            <a href="/products"
               class="{{ request()->is('products') ? 'text-blue-600 font-semibold' : 'hover:text-blue-500' }}">
               Produk
            </a>

            <a href="/kontak"
               class="{{ request()->is('kontak') ? 'text-blue-600 font-semibold' : 'hover:text-blue-500' }}">
               Kontak
            </a>

        </div>
    </div>
</nav>
@endif

<!-- CONTENT -->
<main class="flex-1 p-6">
    @yield('content')
</main>

<!-- ACTIVATE ICON -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        lucide.createIcons();
    });
</script>

</body>
</html>


@include('components.footer')