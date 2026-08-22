@extends('layouts.admin')
@section('page-title', 'Statistik')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 p-0 pt-2">

            <div class="row">
                <!-- Order Statistics -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Total Barang</h5>
                                <small class="text-muted">
                                    Total kuantitas barang yang ada di <strong>SMK Assalaam</strong>
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                    <a class="dropdown-item" href="{{ route('barang.index') }}">
                                        <i class="bx bx-show me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex flex-column align-items-left gap-1">
                                    <h2 class="mt-2 mb-1">{{ $barangStok }}</h2>
                                    <span>Jumlah Terbanyak</span>
                                </div>
                                <div id="orderStatisticsChart"></div>
                            </div>
                            <ul class="p-0 m-0">
                                @foreach ($barangDetail as $barang)
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('/image/barang/' . $barang->foto) }}"
                                                alt="{{ $barang->nama_barang }}" class="rounded" width="40"
                                                height="40" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">{{ $barang->nama }}</h6>
                                                <small class="text-muted">{{ $barang->merek }}</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">{{ number_format($barang->stok) }} stok</small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <!--/ Order Statistics -->

                <!-- Expense Overview -->
                <div class="col-md-6 col-lg-4 order-1 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <ul class="nav nav-pills d-flex justify-content-center" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" id="barangMasukTab"
                                        data-chart-type="masuk">
                                        Barang Masuk
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" id="barangKeluarTab"
                                        data-chart-type="keluar">
                                        Barang Keluar
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body px-0">
                            <!-- Dynamic info section -->
                            <div class="d-flex p-4 pt-3">
                                <div class="avatar flex-shrink-0 me-3">
                                    <img src="../admin/assets/img/icons/unicons/wallet.png" alt="User" />
                                </div>
                                <div>
                                    <small class="text-muted d-block" id="balanceLabel">Barang Masuk</small>
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0 me-1 text-success" id="balanceAmount">0</h6>
                                        <small class="text-success fw-semibold" id="balanceIcon">
                                            <i class="bx bx-chevron-up"></i>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart container always visible -->
                            <div id="incomeChart"></div>

                            <div class="d-flex justify-content-center pt-4 gap-2">
                                <div class="flex-shrink-0">
                                    <div id="expensesOfWeek"></div>
                                </div>
                                <div class="text-center">
                                    <p class="mb-n1 mt-1">Total barang masuk & keluar minggu ini</p>

                                    @php
                                        use Carbon\Carbon;
                                        $startDate = Carbon::now()->subDays(7)->translatedFormat('d F Y');
                                        $endDate = Carbon::now()->translatedFormat('d F Y');
                                    @endphp

                                    <small class="text-muted">Periode: {{ $startDate }} s/d {{ $endDate }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--/ Expense Overview -->

                <!-- Transactions -->
                <div class="col-md-6 col-lg-4 order-2 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Riwayat Peminjaman</h5>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                    <a class="dropdown-item" href="{{ route('peminjaman.index') }}">
                                        <i class="bx bx-show me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="p-0 m-0 scrollable-list">
                                @foreach ($peminjamanDetail as $peminjaman)
                                    <li class="d-flex mb-4 pb-3">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('image/barang/' . $peminjaman->barang->foto) }}"
                                                alt="Foto Barang" class="rounded" width="40" height="40" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                {{-- Status --}}
                                                <small class="text-muted d-block mb-1">
                                                    <span
                                                        class="{{ $peminjaman->status == 'Sedang Dipinjam' ? 'text-danger' : 'text-success' }}">
                                                        {{ $peminjaman->status }}
                                                    </span>
                                                </small>
                                                {{-- Nama Barang --}}
                                                <h6 class="mb-0">{{ $peminjaman->barang->nama }} - <i
                                                        class="text-muted">{{ \Illuminate\Support\Str::limit($peminjaman->nama_peminjam, 8, '') }}</i>...
                                                </h6>
                                            </div>
                                            {{-- Jumlah --}}
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">{{ $peminjaman->jumlah }}</h6>
                                                <span class="text-muted">unit</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                </div>
                <!--/ Transactions -->
            </div>
        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        'use strict';

        document.addEventListener('DOMContentLoaded', function() {
            let cardColor = config.colors.white,
                headingColor = config.colors.headingColor,
                axisColor = config.colors.axisColor,
                borderColor = config.colors.borderColor,
                shadeColor = config.colors.primary;

            const incomeChartEl = document.querySelector('#incomeChart');

            // Get data from Laravel
            const stokMasukData = @json($stokChartData);
            const stokMasukLabels = @json($stokChartLabels);
            const stokKeluarData = @json($stokChartData2);
            const stokKeluarLabels = @json($stokChartLabels2);

            // Get total data from Laravel
            const totalStokMasuk = @json($totalStokMasuk);
            const totalStokKeluar = @json($totalStokKeluar);

            // Debug: Check if data exists
            console.log('Stok Masuk Data:', stokMasukData);
            console.log('Stok Keluar Data:', stokKeluarData);
            console.log('Stok Masuk Labels:', stokMasukLabels);
            console.log('Stok Keluar Labels:', stokKeluarLabels);

            let incomeChart = null;

            function createChart(data, labels, name) {
                const chartConfig = {
                    series: [{
                        name: name,
                        data: data
                    }],
                    chart: {
                        height: 215,
                        parentHeightOffset: 0,
                        parentWidthOffset: 0,
                        toolbar: {
                            show: false
                        },
                        type: 'area',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    legend: {
                        show: false
                    },
                    markers: {
                        size: 6,
                        colors: 'transparent',
                        strokeColors: 'transparent',
                        strokeWidth: 4,
                        hover: {
                            size: 7
                        }
                    },
                    colors: [config.colors.primary],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: shadeColor,
                            shadeIntensity: 0.6,
                            opacityFrom: 0.5,
                            opacityTo: 0.25,
                            stops: [0, 95, 100]
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 3,
                        padding: {
                            top: -20,
                            bottom: -8,
                            left: -10,
                            right: 8
                        }
                    },
                    xaxis: {
                        categories: labels,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            show: true,
                            style: {
                                fontSize: '10px',
                                colors: axisColor
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            show: false
                        },
                        min: 0,
                        tickAmount: 4
                    }
                };

                if (incomeChart) {
                    incomeChart.destroy();
                }

                incomeChart = new ApexCharts(incomeChartEl, chartConfig);
                incomeChart.render();
            }

            function updateChart(data, labels, name) {
                if (incomeChart) {
                    incomeChart.updateOptions({
                        series: [{
                            name: name,
                            data: data
                        }],
                        xaxis: {
                            categories: labels,
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                            labels: {
                                show: true,
                                style: {
                                    fontSize: '10px',
                                    colors: axisColor
                                }
                            }
                        }
                    }, true, true); // redrawPaths = true, animate = true
                }
            }

            function updateBalanceInfo(type, total) {
                const balanceLabel = document.getElementById('balanceLabel');
                const balanceAmount = document.getElementById('balanceAmount');
                const balanceIcon = document.getElementById('balanceIcon');

                if (type === 'masuk') {
                    balanceLabel.textContent = 'Total Barang Masuk';
                    balanceAmount.textContent = total.toLocaleString();
                    balanceAmount.className = 'mb-0 me-1 text-success';
                    balanceIcon.className = 'text-success fw-semibold';
                    balanceIcon.innerHTML = '<i class="bx bx-chevron-up"></i>';
                } else {
                    balanceLabel.textContent = 'Total Barang Keluar';
                    balanceAmount.textContent = total.toLocaleString();
                    balanceAmount.className = 'mb-0 me-1 text-danger';
                    balanceIcon.className = 'text-danger fw-semibold';
                    balanceIcon.innerHTML = '<i class="bx bx-chevron-down"></i>';
                }
            }

            // Initialize chart with Barang Masuk data
            if (incomeChartEl && stokMasukData && stokMasukLabels) {
                createChart(stokMasukData, stokMasukLabels, 'Stok Masuk');
                updateBalanceInfo('masuk', totalStokMasuk);
            }

            // Add event listeners for tab switching
            const barangMasukTab = document.getElementById('barangMasukTab');
            const barangKeluarTab = document.getElementById('barangKeluarTab');

            if (barangMasukTab) {
                barangMasukTab.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Update active tab
                    document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                    this.classList.add('active');

                    console.log('Switching to Barang Masuk');
                    updateChart(stokMasukData, stokMasukLabels, 'Stok Masuk');
                    updateBalanceInfo('masuk', totalStokMasuk);
                });
            }

            if (barangKeluarTab) {
                barangKeluarTab.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Update active tab
                    document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                    this.classList.add('active');

                    console.log('Switching to Barang Keluar');
                    updateChart(stokKeluarData, stokKeluarLabels, 'Stok Keluar');
                    updateBalanceInfo('keluar', totalStokKeluar);
                });
            }
        });
    </script>


@endsection
