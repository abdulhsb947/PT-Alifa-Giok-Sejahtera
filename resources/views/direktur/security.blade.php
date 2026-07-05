@extends('layouts.direktur')

@section('title', 'Laporan Log Keamanan')
@section('subtitle', 'Monitoring log keamanan sistem')

@section('content')

<div class="mb-6">

    <h1 class="text-2xl font-bold">
        Log Keamanan
    </h1>

    <p class="text-gray-500">
        Riwayat aktivitas login seluruh pengguna sistem.
    </p>

</div>

{{-- =========================== --}}
{{-- RINGKASAN --}}
{{-- =========================== --}}

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Total Aktivitas
        </p>

        <h2 class="text-3xl font-bold">

            {{ $logs->count() }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Login Berhasil
        </p>

        <h2 class="text-3xl font-bold text-green-600">

            {{ $logs->where('status','success')->count() }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Login Gagal
        </p>

        <h2 class="text-3xl font-bold text-red-600">

            {{ $logs->where('status','failed')->count() }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p class="text-gray-500 text-sm">
            Pengguna Unik
        </p>

        <h2 class="text-3xl font-bold text-blue-600">

            {{ $logs->pluck('email')->unique()->count() }}

        </h2>

    </div>

</div>

{{-- =========================== --}}
{{-- TABEL --}}
{{-- =========================== --}}

<div class="bg-white rounded-xl shadow overflow-hidden">

    <div class="overflow-x-auto">

        <table class="min-w-full table-fixed">

            <thead class="bg-gray-100 text-gray-700">

                <tr>

                    <th class="w-16 px-4 py-3 text-center">
                        No
                    </th>

                    <th class="w-72 px-4 py-3 text-left">
                        Email
                    </th>

                    <th class="w-40 px-4 py-3 text-center">
                        Status Login
                    </th>

                    <th class="w-56 px-4 py-3 text-center">
                        Alamat IP
                    </th>

                    <th class="w-48 px-4 py-3 text-center">
                        Waktu Login
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($logs as $index => $log)

                <tr class="border-t hover:bg-blue-50 transition">

                    <td class="px-4 py-3 text-center font-semibold">

                        {{ $index + 1 }}

                    </td>

                    <td class="px-4 py-3">

                        {{ $log->email }}

                    </td>

                    <td class="px-4 py-3 text-center">

                        @if($log->status == 'success')

                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

                                Berhasil

                            </span>

                        @else

                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

                                Gagal

                            </span>

                        @endif

                    </td>

                    <td class="px-4 py-3 text-center font-mono text-gray-700">

                        {{ $log->ip_address }}

                    </td>

                    <td class="px-4 py-3 text-center whitespace-nowrap">

                        {{ optional($log->created_at)->format('d M Y H:i') }}

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="5"
                        class="text-center py-10 text-gray-500">

                        Belum ada data log keamanan.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection