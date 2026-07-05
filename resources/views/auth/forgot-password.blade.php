@extends('layouts.app')

@section('content')

<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">Atur Ulang Kata Sandi</h2>

    <form method="POST" action="/forgot-password">
        @csrf

        <!-- EMAIL -->
        <input type="email" name="email" placeholder="Email"
            class="w-full border p-2 mb-3 rounded" required>

        <!-- PASSWORD -->
        <div class="relative">
            <input type="password" id="password" name="password"
                placeholder="Kata Sandi Baru"
                class="w-full border p-2 rounded mb-2" required>

            <!-- 👁️ SHOW PASSWORD -->
            <button type="button" onclick="togglePassword('password')" 
                class="absolute right-3 top-1/2 -translate-y-1/2">
                👁️
            </button>
        </div>

        <!-- CONFIRM PASSWORD -->
        <div class="relative">
            <input type="password" id="confirmPassword" name="password_confirmation"
                placeholder="Kofirmasi Kata Sandi Baru"
                class="w-full border p-2 rounded mb-2" required>

            <button type="button" onclick="togglePassword('confirmPassword')" 
                class="absolute right-3 top-1/2 -translate-y-1/2">
                👁️
            </button>
        </div>

        <!-- VALIDATION LIST -->
        <div class="text-sm space-y-1 mb-3">

            <p id="length" class="text-red-500">❌ Minimal 8 karakter</p>
            <p id="uppercase" class="text-red-500">❌ Huruf besar</p>
            <p id="number" class="text-red-500">❌ Nomor</p>
            <p id="symbol" class="text-red-500">❌ Simbol (@$!%*#?)</p>
            <p id="match" class="text-red-500">❌ Kata sandi sama</p>

        </div>

        <button class="bg-blue-600 text-white w-full p-2 rounded">
            Atur Ulang Kata Sandi
        </button>

    </form>

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

        // CHECKS
        const checkLength = value.length >= 8;
        const checkUpper = /[A-Z]/.test(value);
        const checkNumber = /[0-9]/.test(value);
        const checkSymbol = /[@$!%*#?&,.]/.test(value);
        const checkMatch = value === confirmPassword.value && value !== "";

        // LENGTH
        length.innerHTML = checkLength ? "✅ Minimum 8 characters" : "❌ Minimum 8 characters";
        length.className = checkLength ? "text-green-600" : "text-red-500";

        // UPPERCASE
        uppercase.innerHTML = checkUpper ? "✅ Uppercase letter" : "❌ Uppercase letter";
        uppercase.className = checkUpper ? "text-green-600" : "text-red-500";

        // NUMBER
        number.innerHTML = checkNumber ? "✅ Number" : "❌ Number";
        number.className = checkNumber ? "text-green-600" : "text-red-500";

        // SYMBOL
        symbol.innerHTML = checkSymbol ? "✅ Symbol (@$!%*#?)" : "❌ Symbol (@$!%*#?)";
        symbol.className = checkSymbol ? "text-green-600" : "text-red-500";

        // MATCH
        match.innerHTML = checkMatch ? "✅ Password match" : "❌ Password match";
        match.className = checkMatch ? "text-green-600" : "text-red-500";
    }

    password.addEventListener("keyup", validate);
    confirmPassword.addEventListener("keyup", validate);

});
</script>

@endsection