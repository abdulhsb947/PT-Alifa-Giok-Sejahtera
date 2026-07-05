@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto py-8 space-y-6">

    {{-- ========================= --}}
    {{-- ALERT --}}
    {{-- ========================= --}}
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

    {{-- ========================= --}}
    {{-- JUDUL --}}
    {{-- ========================= --}}
    <div>
        <h1 class="text-3xl font-bold">Pengaturan Admin</h1>
        <p class="text-gray-500">Kelola informasi akun administrator</p>
    </div>

    {{-- ========================= --}}
    {{-- PROFIL --}}
    {{-- ========================= --}}
    <div class="bg-white p-6 rounded-xl shadow">

        <h2 class="font-bold mb-4">Informasi Profil</h2>

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- FOTO PROFIL --}}
            <div class="flex items-center gap-4 mb-4">

                <img id="previewImage"
                    src="{{ auth()->user()->photo 
                        ? asset('storage/profile/' . auth()->user()->photo) 
                        : 'https://via.placeholder.com/80' }}"
                    class="w-20 h-20 rounded-full object-cover border">

                <input type="file" name="photo" accept="image/*"
                    onchange="previewFile(event)"
                    class="border p-2 rounded">
            </div>

            <div class="grid md:grid-cols-2 gap-4">

                <div>
                    <label>Nama Lengkap</label>
                    <input type="text" name="name"
                        value="{{ auth()->user()->name }}"
                        class="w-full border p-2 rounded">
                </div>

                <div>
                    <label>Email</label>
                    <input type="email" name="email"
                        value="{{ auth()->user()->email }}"
                        class="w-full border p-2 rounded">
                </div>

            </div>

            <div class="grid md:grid-cols-2 gap-4 mt-4">

                <div>
                    <label>No. Telepon</label>
                    <input type="text" name="phone"
                        value="{{ auth()->user()->phone }}"
                        class="w-full border p-2 rounded">
                </div>

                <div>
                    <label>Perusahaan</label>
                    <input type="text" name="company"
                        value="{{ auth()->user()->company }}"
                        class="w-full border p-2 rounded">
                </div>

            </div>

            <div class="mt-4">
                <label>Alamat</label>
                <input type="text" name="address"
                    value="{{ auth()->user()->address }}"
                    class="w-full border p-2 rounded">
            </div>

            <div class="text-right mt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

    {{-- ========================= --}}
    {{-- PASSWORD --}}
    {{-- ========================= --}}
    <div class="bg-white p-6 rounded-xl shadow">

        <h2 class="font-bold mb-4">Ubah Kata Sandi</h2>

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf

            <div class="mb-4">
                <label>Kata Sandi Lama</label>
                <input type="password" name="current_password"
                    class="w-full border p-2 rounded">
            </div>

            <div class="grid md:grid-cols-2 gap-4">

                <div>
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password"
                        class="w-full border p-2 rounded">
                </div>

                <div>
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border p-2 rounded">
                </div>

            </div>

            <div class="text-right mt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Perbarui Kata Sandi
                </button>
            </div>

        </form>

    </div>

    {{-- ========================= --}}
    {{-- INFORMASI AKUN --}}
    {{-- ========================= --}}
    <div class="bg-white p-6 rounded-xl shadow">

        <h2 class="font-bold mb-4">Informasi Akun</h2>

        <div class="grid md:grid-cols-2 gap-4 text-sm">

            <div>
                <p class="text-gray-500">Peran</p>
                <p class="font-medium capitalize">{{ auth()->user()->role }}</p>
            </div>

            <div>
                <p class="text-gray-500">Bergabung Sejak</p>
                <p class="font-medium">
                    {{ auth()->user()->created_at->format('d M Y') }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">ID Pengguna</p>
                <p class="font-medium">{{ auth()->user()->id }}</p>
            </div>

            <div>
                <p class="text-gray-500">Email</p>
                <p class="font-medium">{{ auth()->user()->email }}</p>
            </div>

        </div>

    </div>

</div>

{{-- ========================= --}}
{{-- SCRIPT PREVIEW GAMBAR --}}
{{-- ========================= --}}
<script>
function previewFile(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('previewImage').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

@endsection