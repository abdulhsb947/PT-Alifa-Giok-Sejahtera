@extends('layouts.app')

@section('content')

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
             ALIFA GIOK SEJAHTERA<span class="text-blue-300"></span>
        </span>
    </a>

    <div>
        <h1 class="text-4xl font-bold mb-4">
            Bergabunglah denganPT Alifa Giok Sejahtera
        </h1>
        <p class="text-white/70">
            Buat akun Anda dan kelola proyek dengan mudah
        </p>
    </div>

    <p class="text-white/50 text-sm">
        © 2024 ScaffoldPro
    </p>

</div>

<!-- RIGHT -->
<div class="flex-1 flex items-center justify-center p-6 md:p-12 bg-white overflow-y-auto">

<div class="w-full max-w-md">

<!-- MOBILE LOGO -->
<div class="lg:hidden mb-8 text-center">
    <a href="/" class="inline-flex items-center gap-2">
        <div class="w-10 h-10 bg-blue-600 rounded flex items-center justify-center text-white">
            <i data-lucide="layers"></i>
        </div>
        <span class="font-bold text-xl">
            Scaffold<span class="text-blue-600">Pro</span>
        </span>
    </a>
</div>

<!-- TITLE -->
<div class="text-center mb-6">
    <h2 class="text-3xl font-bold mb-2">Buat Akun</h2>
    <p class="text-gray-500">Daftar untuk memulai</p>
</div>

<!-- FORM -->
<form method="POST" action="/register" class="space-y-4">
@csrf

<div>
    <label>Nama Lengkap *</label>
    <input name="name" type="text" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500" required>
</div>

<div>
    <label>Email *</label>
    <input name="email" type="email" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500" required>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label>Telepon</label>
        <input name="phone" type="text" class="w-full border px-3 py-2 rounded">
    </div>

    <div>
        <label>Perusahaan</label>
        <input name="company" type="text" class="w-full border px-3 py-2 rounded">
    </div>

</div>

<!-- PASSWORD -->
<div>
    <label>Kata Sandi *</label>
    <div class="relative">
        <input id="password" name="password" type="password"
            class="w-full border px-3 py-2 rounded" required>

        <button type="button" onclick="togglePassword('password')" 
            class="absolute right-3 top-1/2 -translate-y-1/2">
            👁️
        </button>
    </div>
</div>

<!-- CONFIRM PASSWORD -->
<div>
    <label>Konfirmasi Kata Sandi *</label>
    <div class="relative">
        <input id="confirmPassword" name="password_confirmation" type="password"
            class="w-full border px-3 py-2 rounded" required>

        <button type="button" onclick="togglePassword('confirmPassword')" 
            class="absolute right-3 top-1/2 -translate-y-1/2">
            👁️
        </button>
    </div>
</div>

<!-- VALIDATION -->
<div class="text-sm space-y-1">
    <p id="length" class="text-red-500">❌ Minimal 8 karakter</p>
    <p id="uppercase" class="text-red-500">❌ Huruf besar</p>
    <p id="number" class="text-red-500">❌ Angka</p>
    <p id="symbol" class="text-red-500">❌ Simbol</p>
    <p id="match" class="text-red-500">❌ Kata sandi sama</p>
</div>

<!-- TERMS -->
<div class="flex items-start gap-2">
    <input type="checkbox" required>
    <label class="text-sm text-gray-500">
        Saya menyetujui
        <a href="/terms" class="text-blue-600">Ketentuan</a> dan
        <a href="/privacy" class="text-blue-600">Kebijakan Privasi</a>
    </label>
</div>

<button class="w-full bg-blue-600 text-white py-3 rounded flex items-center justify-center gap-2 hover:bg-blue-700">
    Buat Akun
    <i data-lucide="arrow-right"></i>
</button>

</form>

<p class="mt-6 text-center text-gray-500">
    Sudah punya akun?
    <a href="/login" class="text-blue-600">Masuk</a>
</p>

</div>
</div>

</div>

<!-- SCRIPT -->
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

document.addEventListener("DOMContentLoaded", function () {

    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");

    const length = document.getElementById("length");
    const uppercase = document.getElementById("uppercase");
    const number = document.getElementById("number");
    const symbol = document.getElementById("symbol");
    const match = document.getElementById("match");

    function validate() {
        const value = password.value;

        const checkLength = value.length >= 8;
        const checkUpper = /[A-Z]/.test(value);
        const checkNumber = /[0-9]/.test(value);
        const checkSymbol = /[@$!%*#?&]/.test(value);
        const checkMatch = value === confirmPassword.value && value !== "";

        length.innerHTML = checkLength ? "✅ Minimum 8 characters" : "❌ Minimum 8 characters";
        length.className = checkLength ? "text-green-600" : "text-red-500";

        uppercase.innerHTML = checkUpper ? "✅ Uppercase letter" : "❌ Uppercase letter";
        uppercase.className = checkUpper ? "text-green-600" : "text-red-500";

        number.innerHTML = checkNumber ? "✅ Number" : "❌ Number";
        number.className = checkNumber ? "text-green-600" : "text-red-500";

        symbol.innerHTML = checkSymbol ? "✅ Symbol" : "❌ Symbol";
        symbol.className = checkSymbol ? "text-green-600" : "text-red-500";

        match.innerHTML = checkMatch ? "✅ Password match" : "❌ Password match";
        match.className = checkMatch ? "text-green-600" : "text-red-500";
    }

    password.addEventListener("keyup", validate);
    confirmPassword.addEventListener("keyup", validate);

});
</script>

@endsection