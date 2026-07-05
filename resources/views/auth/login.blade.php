@extends('layouts.app')

@section('content')

{{-- ERROR --}}
@if(session('error'))
<div id="alertBox" 
    class="fixed top-0 left-0 w-full bg-red-500 text-white text-center py-3 shadow-lg transform -translate-y-full transition-all duration-500 z-50">
    ⚠️ {{ session('error') }}
</div>
@endif

{{-- SUCCESS --}}
@if(session('success'))
<div id="successBox"
    class="fixed top-0 left-0 w-full bg-green-500 text-white text-center py-3 shadow-lg transform -translate-y-full transition-all duration-500 z-50">
    ✅ {{ session('success') }}
</div>
@endif

<div class="min-h-screen flex flex-col lg:flex-row">

<!-- LEFT -->
<div class="hidden lg:flex lg:w-1/2 gradient-hero p-12 flex-col justify-between text-white">

    <a href="/" class="flex items-center gap-2">
        <div class="w-10 h-10 bg-white rounded flex items-center justify-center">
            <img src="{{ asset('storage/image/LEGO_logo.svg.png') }}" 
             alt="Logo"
             class="w-full h-full object-cover">
        </div>
        <span class="font-bold text-xl">
             ALIFA GIOK SEJAHTERA<span class="text-blue-300">Pro</span>
        </span>
    </a>

    <div>
        <h1 class="text-4xl font-bold mb-4">
            Kelola Properti Sewa Anda
        </h1>
        <p class="text-white/70">
            Kelola proyek scaffolding dengan mudah dan efisien.
        </p>
    </div>

    <p class="text-white/50 text-sm">
        © 2024 ScaffoldPro
    </p>

</div>

<!-- RIGHT -->
<div class="flex-1 flex items-center justify-center px-4 py-10 md:px-10 bg-white">

<div class="w-full max-w-md">

<!-- MOBILE LOGO -->
<div class="lg:hidden mb-8 text-center">
    <a href="/" class="inline-flex items-center gap-2">
        <div class="w-10 h-10 bg-blue-600 rounded flex items-center justify-center text-white">
            <i data-lucide="layers"></i>
        </div>
        <span class="font-bold text-xl">
            PT ALIFA GIOK SEJAHTERA<span class="text-blue-600"></span>
        </span>
    </a>
</div>

<!-- TITLE -->
<div class="text-center mb-8">
    <h2 class="text-2xl md:text-3xl font-bold mb-2">Masuk</h2>
    <p class="text-gray-500 text-sm md:text-base">
        Selamat datang kembali, silakan masuk
    </p>
</div>

<!-- FORM -->
<form method="POST" action="/login" class="space-y-4">
@csrf

<div>
    <label class="text-sm">Email</label>
    <input id="email" name="email" type="email"
        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500" required>
</div>

<div>
    <div class="flex justify-between text-sm">
        <label>Kata Sandi</label>

        <a href="/forgot-password" class="text-blue-600 hover:underline">
            Lupa Kata Sandi?
        </a>
    </div>

    <div class="relative">
        <input id="password" name="password" type="password"
            class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500" required>

        <button type="button" onclick="togglePassword()" 
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
            👁️
        </button>
    </div>
</div>

<button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded flex items-center justify-center gap-2 transition">
    Masuk
    <i data-lucide="arrow-right"></i>
</button>

</form>

<p class="mt-6 text-center text-gray-500 text-sm">
    Belum punya akun?
    <a href="/register" class="text-blue-600">Buat Akun</a>
</p>

</div>
</div>

</div>

<!-- SCRIPT -->
<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}

document.addEventListener("DOMContentLoaded", function () {

    const alertBox = document.getElementById("alertBox");
    const successBox = document.getElementById("successBox");

    if (alertBox) {
        setTimeout(() => alertBox.classList.remove("-translate-y-full"), 100);
        setTimeout(() => alertBox.classList.add("-translate-y-full"), 3000);
    }

    if (successBox) {
        setTimeout(() => successBox.classList.remove("-translate-y-full"), 100);
        setTimeout(() => successBox.classList.add("-translate-y-full"), 3000);
    }

});
</script>

@endsection