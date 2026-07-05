@extends('layouts.direktur')

@section('title', 'Dashboard Direktur')
@section('subtitle', 'Ringkasan performa bisnis dan operasional')

@section('content')

    <div class="space-y-6">

        {{-- KPI --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-4">

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Total Pesanan
                </p>

                <h2 class="text-3xl font-bold text-blue-600">
                    {{ $totalOrders }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Rental Aktif
                </p>

                <h2 class="text-3xl font-bold text-green-600">
                    {{ $activeRentals }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Total Produk
                </p>

                <h2 class="text-3xl font-bold text-indigo-600">
                    {{ $totalProducts }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Perawatan
                </p>

                <h2 class="text-3xl font-bold text-orange-600">
                    {{ $maintenanceProducts }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Menunggu Verifikasi
                </p>

                <h2 class="text-3xl font-bold text-yellow-600">
                    {{ $pendingPayments }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Pendapatan
                </p>

                <h2 class="text-xl font-bold text-green-700">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-gray-500 text-sm">
                    Total Kerugian
                </p>

                <h2 class="text-xl font-bold text-red-600">
                    Rp {{ number_format($totalLoss, 0, ',', '.') }}
                </h2>
            </div>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-6 mb-10">

                {{-- Grafik Pemesanan --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">
                        Tren Pemesanan
                    </h3>

                    <div id="ordersChart"></div>
                </div>

                {{-- Pendapatan vs Kerugian --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">
                        Pendapatan vs Kerugian
                    </h3>

                    <div id="financeChart"></div>
                </div>

                {{-- Status Rental --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">
                        Status Rental
                    </h3>

                    <div id="rentalChart"></div>
                </div>

                {{-- Top Produk --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">
                        Top Produk Disewa
                    </h3>

                    <div id="productChart"></div>
                </div>
            </div>

        </div>

    </div>

    {{-- INFORMASI --}}
    <div class="grid lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="font-bold text-lg mb-4">
                Ringkasan Operasional
            </h3>

            <div class="space-y-4">

                <div class="flex justify-between">
                    <span>Total Pesanan</span>
                    <strong>{{ $totalOrders }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Rental Aktif</span>
                    <strong>{{ $activeRentals }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Sedang Dalam Perawatan</span>
                    <strong>{{ $maintenanceProducts }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Produk Hilang</span>
                    <strong class="text-red-600">
                        {{ $totalLostProducts }}
                    </strong>
                </div>

                <div class="flex justify-between">
                    <span>Menunggu Verifikasi Pembayaran</span>
                    <strong>{{ $pendingPayments }}</strong>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="font-bold text-lg mb-4">
                Total Pendapatan
            </h3>

            <div class="flex items-center justify-center h-40">

                <div class="text-center">

                    <p class="text-gray-500">
                        Total Pendapatan Disetujui
                    </p>

                    <h2 class="text-4xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    {{-- ORDER TERBARU --}}
    <div class="mt-10 bg-white rounded-xl shadow">

        <div class="p-5 border-b">
            <h3 class="font-bold">
                Pesanan Terbaru
            </h3>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-100">

                    <tr>
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Pelanggan</th>
                        <th class="p-3 text-left">Proyek</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Status</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($recentOrders as $order)
                        <tr class="border-b">

                            <td class="p-3">
                                {{ $order->order_code }}
                            </td>

                            <td class="p-3">
                                {{ $order->user->name ?? '-' }}
                            </td>

                            <td class="p-3">
                                {{ $order->project_name }}
                            </td>

                            <td class="p-3">
                                {{ $order->created_at->format('d M Y') }}
                            </td>

                            <td class="p-3">

                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-600">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center p-6 text-gray-500">
                                Belum ada data
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script type="text/javascript">
        new ApexCharts(
            document.querySelector("#ordersChart"), {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },

                dataLabels: {
                    enabled: false
                },

                stroke: {
                    curve: 'smooth',
                    width: 3
                },

                series: [{
                    name: 'Pesanan',
                    data: [
                        @foreach ($orderChart as $item)
                            {{ $item->total }},
                        @endforeach
                    ]
                }],

                xaxis: {
                    categories: [
                        @foreach ($orderChart as $item)
                            "{{ $item->month }}",
                        @endforeach
                    ]
                },

                legend: {
                    show: false
                }
            }
        ).render();


        new ApexCharts(
            document.querySelector("#financeChart"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },

                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%'
                    }
                },

                dataLabels: {
                    enabled: false
                },

                colors: [
                    '#10B981', // Pendapatan (Hijau)
                    '#EF4444' // Kerugian (Merah)
                ],

                series: [{
                        name: 'Pendapatan',
                        data: [{{ $totalRevenue }}]
                    },
                    {
                        name: 'Kerugian',
                        data: [{{ $totalLoss }}]
                    }
                ],

                xaxis: {
                    categories: ['Keuangan']
                },

                legend: {
                    position: 'bottom'
                }
            }
        ).render();


        new ApexCharts(
            document.querySelector("#rentalChart"), {
                chart: {
                    type: 'donut',
                    height: 320
                },

                series: [
                    {{ $rentalStatus[0] }},
                    {{ $rentalStatus[1] }},
                    {{ $rentalStatus[2] }}
                ],

                labels: [
                    'Dikirim',
                    'Berjalan',
                    'Selesai'
                ],

                legend: {
                    position: 'bottom'
                }
            }
        ).render();


        new ApexCharts(
            document.querySelector("#productChart"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },

                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6
                    }
                },

                dataLabels: {
                    enabled: false
                },

                series: [{
                    name: 'Disewa',
                    data: [
                        @foreach ($topProducts as $product)
                            {{ $product->rented_stock }},
                        @endforeach
                    ]
                }],

                xaxis: {
                    categories: [
                        @foreach ($topProducts as $product)
                            "{{ Str::limit($product->name, 20) }}",
                        @endforeach
                    ]
                },

                legend: {
                    show: false
                }
            }
        ).render();
    </script>

@endsection
