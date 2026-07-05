@extends('layouts.direktur')

@section('content')

<div class="flex justify-between items-center mb-6">

    <div>
        <h1 class="text-2xl font-bold">
            Manajemen Pengguna
        </h1>

        <p class="text-gray-500">
            Kelola seluruh akun pengguna sistem
        </p>
    </div>

    <a href="{{ route('direktur.users.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">

        + Tambah Pengguna

    </a>

</div>

{{-- ========================= --}}
{{-- RINGKASAN --}}
{{-- ========================= --}}

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Pengguna
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $users->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Customer
        </p>

        <h2 class="text-3xl font-bold text-green-600 mt-2">
            {{ $users->where('role','customer')->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Admin
        </p>

        <h2 class="text-3xl font-bold text-blue-600 mt-2">
            {{ $users->where('role','admin')->count() }}
        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Direktur
        </p>

        <h2 class="text-3xl font-bold text-purple-600 mt-2">
            {{ $users->where('role','direktur')->count() }}
        </h2>

    </div>

</div>

{{-- ========================= --}}
{{-- TABEL USER --}}
{{-- ========================= --}}

<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="p-3 text-center w-16">
                    No
                </th>

                <th class="p-3 text-left">
                    Nama
                </th>

                <th class="p-3 text-left">
                    Email
                </th>

                <th class="p-3 text-center">
                    Role
                </th>

                <th class="p-3 text-center">
                    Tanggal Dibuat
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($users as $index => $user)

            <tr class="border-t hover:bg-gray-50 transition">

                <td class="p-3 text-center font-semibold">
                    {{ $index + 1 }}
                </td>

                <td class="p-3">

                    <div class="font-semibold">
                        {{ $user->name }}
                    </div>

                </td>

                <td class="p-3 text-gray-600">
                    {{ $user->email }}
                </td>

                <td class="p-3 text-center">

                    @if($user->role == 'direktur')

                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-medium">
                            Direktur
                        </span>

                    @elseif($user->role == 'admin')

                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                            Admin
                        </span>

                    @elseif($user->role == 'customer')

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                            Customer
                        </span>

                    @else

                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($user->role) }}
                        </span>

                    @endif

                </td>

                <td class="p-3 text-center">

                    {{ optional($user->created_at)->format('d M Y') }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5"
                    class="text-center p-8 text-gray-500">

                    Belum ada data pengguna.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection