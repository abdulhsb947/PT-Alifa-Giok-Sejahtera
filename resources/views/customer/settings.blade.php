@extends('layouts.customer')

@section('content')

@php
$user = auth()->user();
@endphp

<div class="container mx-auto px-4 py-8">
<div class="max-w-3xl mx-auto space-y-6">

<!-- ALERT -->
@if(session('success'))
<div class="bg-green-100 text-green-700 p-3 rounded">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 text-red-700 p-3 rounded">
    {{ session('error') }}
</div>
@endif

<!-- TITLE -->
<div>
    <h1 class="text-2xl md:text-3xl font-bold">Pengaturan Akun</h1>
    <p class="text-gray-500">Kelola informasi akun Anda</p>
</div>

<!-- ====================== -->
<!-- PROFIL -->
<!-- ====================== -->
<div class="bg-white border rounded-xl p-6 space-y-4">

    <h2 class="font-bold flex items-center gap-2">
        <i data-lucide="user"></i>
        Informasi Profil
    </h2>

    <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- FOTO -->
        <div class="flex items-center gap-4">
            <img id="previewImage"
                src="{{ $user->photo ? asset('storage/profile/'.$user->photo) : 'https://via.placeholder.com/80' }}"
                class="w-16 h-16 rounded-full object-cover border">

            <input type="file" name="photo" onchange="previewFile(event)">
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ $user->email }}"
                    class="w-full border px-3 py-2 rounded">
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label>No. Telepon</label>
                <input type="text" name="phone" value="{{ $user->phone ?? '' }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Perusahaan</label>
                <input type="text" name="company" value="{{ $user->company ?? '' }}"
                    class="w-full border px-3 py-2 rounded">
            </div>
        </div>

        <div>
            <label>Alamat</label>
            <input type="text" name="address" value="{{ $user->address ?? '' }}"
                class="w-full border px-3 py-2 rounded">
        </div>

        <div class="text-right">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

<!-- ====================== -->
<!-- PASSWORD -->
<!-- ====================== -->
<div class="bg-white border rounded-xl p-6 space-y-4">

    <h2 class="font-bold flex items-center gap-2">
        <i data-lucide="lock"></i>
        Ubah Kata Sandi
    </h2>

    <form method="POST" action="{{ route('customer.password.update') }}" id="passwordForm" class="space-y-4">
        @csrf

        <div>
            <label>Kata Sandi Saat Ini</label>
            <input type="password" name="current_password"
                class="w-full border px-3 py-2 rounded">
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label>Kata Sandi Baru</label>
                <input type="password" name="password" id="new"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" id="confirm"
                    class="w-full border px-3 py-2 rounded">
            </div>
        </div>

        <div class="text-right">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Perbarui Kata Sandi
            </button>
        </div>

    </form>

</div>

<!-- ====================== -->
<!-- INFO AKUN -->
<!-- ====================== -->
<div class="bg-white border rounded-xl p-6 space-y-4">

    <h2 class="font-bold flex items-center gap-2">
        <i data-lucide="shield"></i>
        Informasi Akun
    </h2>

    <div class="grid sm:grid-cols-2 gap-4 text-sm">

        <div>
            <span class="text-gray-400">Peran</span>
            <p class="font-medium capitalize">{{ $user->role }}</p>
        </div>

        <div>
            <span class="text-gray-400">Bergabung Sejak</span>
            <p class="font-medium">
                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
            </p>
        </div>

        <div>
            <span class="text-gray-400">ID Akun</span>
            <p class="font-medium">{{ $user->id }}</p>
        </div>

        <div>
            <span class="text-gray-400">Email</span>
            <p class="font-medium">{{ $user->email }}</p>
        </div>

    </div>

</div>

</div>
</div>

<!-- PREVIEW FOTO -->
<script>
function previewFile(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('previewImage').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<!-- VALIDASI PASSWORD -->
<script>
document.getElementById('passwordForm').addEventListener('submit', function(e){
    const newPass = document.getElementById('new').value;
    const confirm = document.getElementById('confirm').value;

    if(newPass !== confirm){
        e.preventDefault();
        alert('Kata sandi tidak sama!');
    }

    if(newPass.length < 8){
        e.preventDefault();
        alert('Kata sandi minimal 8 karakter!');
    }
});
</script>

@endsection