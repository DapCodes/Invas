@extends('layouts.admin')
@section('page-title', 'Beranda')

@section('content')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="row g-0 align-items-center">
                    <div class="col-md-7">
                        <div class="card-body animate__animated animate__fadeIn">
                            @if (Auth::user()->status_user == 'admin')
                                <small class="text-muted fst-italic">Admin</small>
                            @endif
                            @if (Auth::user()->status_user != 'admin')
                                <small class="text-muted fst-italic">Petugas {{ Auth::user()->status_user }}</small>
                            @endif

                            <h6 class="card-title text-muted mb-2">Selamat Datang <strong>{{ Auth::user()->name }}</strong></h6>

                            <!-- Teks Animasi -->
                            <div style="height: 40px; width: 100%; overflow: hidden; border-radius: 8px; margin-top: 10px;">
                                <h2 id="typing" style="
                                    margin: 0;
                                    white-space: nowrap;
                                    font-weight: bold;
                                    background: linear-gradient(90deg, #6a6aff, #00c9a7,rgb(255, 238, 0));
                                    -webkit-background-clip: text;
                                    -webkit-text-fill-color: transparent;
                                    background-clip: text;
                                    color: transparent;
                                ">
                                </h2>
                            </div>

                            <!-- Deskripsi -->
                            <p class="mb-3 mt-3 text-secondary">
                                <strong>INVAS</strong> mempermudah pendataan logistikmu secara efisien dan terstruktur.<br>
                                Ayo mulai cek statistik logistikmu hari ini!
                            </p>

                            <!-- Tombol Aksi -->
                            <a href="{{ route('admin.statistik') }}" class="btn btn-outline-primary btn-s">
                                <i class="bx bx-grid-alt"></i> Lihat Statistik!
                            </a>
                        </div>
                    </div>

                    <!-- Ilustrasi -->
                    <div class="col-md-5 text-center">
                        <img src="../admin/assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Ilustrasi Pengguna"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                            class="img-fluid p-3 animate__animated animate__fadeInRight"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

@php
    use Carbon\Carbon;
    $startDate = Carbon::now()->subDays(7)->translatedFormat('d F Y');
    $endDate = Carbon::now()->translatedFormat('d F Y');

    $cards = [
        [
            'title' => 'Data Barang Masuk',
            'total' => $barangMasuk,
            'stok' => $totalStokMasuk,
            'route' => 'brg-masuk.index',
            'icon' => '/admin/assets/img/gif-icons/masuk.png',
            'bg' => 'bg-primary'
        ],
        [
            'title' => 'Data Barang Keluar',
            'total' => $barangKeluar,
            'stok' => $totalStokKeluar,
            'route' => 'brg-keluar.index',
            'icon' => '/admin/assets/img/gif-icons/keluar.png',
            'bg' => 'bg-warning'
        ],
        [
            'title' => 'Data Peminjaman',
            'total' => $peminjaman,
            'stok' => $peminjamanStok,
            'route' => 'peminjaman.index',
            'icon' => '/admin/assets/img/gif-icons/pinjam.png',
            'bg' => 'bg-info'
        ],
        [
            'title' => 'Data Pengembalian',
            'total' => $pengembalian,
            'stok' => $pengembalianStok,
            'route' => 'pengembalian.index',
            'icon' => '/admin/assets/img/gif-icons/kembali.png',
            'bg' => 'bg-success'
        ],
    ];

    // Filter kartu dengan data baru
    $cards = array_filter($cards, fn($card) => $card['total'] > 0);
@endphp

<div class="mb-4">
    <h5 class="fw-bold">Data Terbaru 1 Minggu Terakhir 
        @if (Auth::user()->is_admin === 1)
            <span class="badge bg-primary ms-1">Admin - Keseluruhan</span>
        @else
            <span class="badge bg-secondary ms-1">{{ Auth::user()->status_user }}</span>
        @endif
    </h5>
    <p class="text-muted mb-0"><small>Periode: {{ $startDate }} s/d {{ $endDate }}</small></p>
</div>

<div class="row">
    @forelse ($cards as $card)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset($card['icon']) }}" alt="icon" style="width: 32px; height: 32px;">
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $card['title'] }}</h6>
                            <small class="text-muted">Data dan stok terbaru</small>
                        </div>
                        <div class="ms-auto">
                            <div class="dropdown">
                                <button class="btn btn-sm text-muted" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route($card['route']) }}">
                                            <i class="bx bx-show me-2"></i>Lihat Detail
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-muted small">Total Data</div>
                            <h5 class="fw-bold text-{{ $card['total'] > 0 ? 'success' : 'danger' }}">{{ $card['total'] }}</h5>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Total Stok</div>
                            <h5 class="fw-bold text-{{ $card['stok'] > 0 ? 'primary' : 'danger' }}">{{ $card['stok'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light border d-flex align-items-center" role="alert">
                <i class="bx bx-info-circle text-primary fs-4 me-2"></i>
                <div>
                    Tidak ada data baru yang masuk selama 7 hari terakhir. Silakan cek kembali nanti.
                </div>
            </div>
        </div>
    @endforelse
</div>





@if (session('success_login'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast show bg-white shadow border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="{{ asset('admin/assets/img/favicon/gudangku-icon.ico') }}" class="rounded me-2" alt="Logo" width="18">
                <strong class="me-auto text-dark">Invas</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-dark">
                {{ session('success_login') }}
            </div>
        </div>
    </div>
@endif

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        const texts = ["Selamat Datang di INVAS!!", "Inventaris SMK Assalaam"];
    let count = 0;
    let index = 0;
    let currentText = "";
    let letter = "";

    function type() {
      if (count === texts.length) {
        count = 0;
      }

      currentText = texts[count];
      letter = currentText.slice(0, ++index);

      document.getElementById("typing").textContent = letter;

      if (letter.length === currentText.length) {
        setTimeout(() => {
          erase();
        }, 2000); // jeda 2 detik sebelum hapus
      } else {
        setTimeout(type, 100); // kecepatan ngetik
      }
    }

    function erase() {
      letter = currentText.slice(0, --index);
      document.getElementById("typing").textContent = letter;

      if (letter.length === 0) {
        count++;
        setTimeout(type, 300); // jeda sebelum lanjut teks berikutnya
      } else {
        setTimeout(erase, 50); // kecepatan hapus
      }
    }

    // mulai
    type();
    </script>

@endsection















<!--  ini chart statistik
            <div class="col-lg-12 mt-4">
                <div class="card p-4">
                    <div class="card-body">
                        <div id="chart" style="height: 390.9px;"></div>
                    </div>
                </div>
            </div> 
            
            
                    //statistik semua data
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Jumlah',
                    data: {!! json_encode($chartData['series']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                    labels: {
                        style: {
                            fontSize: '10px',
                            colors: '#6B7280'
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        borderRadiusApplication: 'end',
                        borderRadiusWhenStacked: 'last',
                        distributed: true,
                        columnWidth: '55%',
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                        colors: ['#fff']
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + ' data';
                        }
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    itemMargin: {
                        horizontal: 10,
                        vertical: 8 // memberi jarak antar item legend
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        });-->







        <!-- 
        data peminjaman pengembalian
<div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Data Peminjaman / Pengembalian</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column gap-1" style="position: relative; bottom: 15px;">
                            <span>Peminjaman <strong>{{ $peminjaman }}</strong></span>
                            <span>Pengembalian <strong>{{ $pengembalian }}</strong></span>
                        </div>
                        <div id="orderStatisticsChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="../admin/assets/img/gif-icons/kembali.png" alt="">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Peminjaman</h6>
                                    <small class="text-muted">Total peminjaman barang</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $peminjaman }}x</small>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="../admin/assets/img/gif-icons/pinjam.png" alt="">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Pengembalian</h6>
                                    <small class="text-muted">Total pengembalian barang</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $pengembalian }}x</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>




                document.addEventListener('DOMContentLoaded', function() {
            const config = {
                colors: {
                    primary: '#3B82F6',
                    secondary: '#64748B',
                    info: '#0EA5E9',
                    success: '#10B981',
                    danger: '#EF4444'
                }
            };
            const cardColor = '#fff';
            const headingColor = '#111827';
            const axisColor = '#6B7280';

            const chartOrderStatistics = document.querySelector('#orderStatisticsChart');
            const orderChartConfig = {
                chart: {
                    height: 165,
                    width: 130,
                    type: 'donut'
                },
                labels: {!! json_encode($chartData['pinjamkembali']) !!},
                series: {!! json_encode($chartData['pinjamkembaliseries']) !!},
                colors: [config.colors.danger, config.colors.success],
                stroke: {
                    width: 5,
                    colors: cardColor
                },
                dataLabels: {
                    enabled: false,
                    formatter: function(val, opt) {
                        return parseInt(val) + '%';
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    padding: {
                        top: 0,
                        bottom: 0,
                        right: 15
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '85%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '1.5rem',
                                    fontFamily: 'Public Sans',
                                    color: headingColor,
                                    offsetY: -15,
                                    formatter: function(val) {
                                        return parseInt(val);
                                    }
                                },
                                name: {
                                    offsetY: 20,
                                    fontFamily: 'Public Sans'
                                },
                                total: {
                                    show: true,
                                    fontSize: '0.8125rem',
                                    color: axisColor,
                                    label: 'Total',
                                    formatter: function(w) {
                                        return '{{ $peminjaman + $pengembalian }}';
                                    }
                                }
                            }
                        }
                    }
                }
            };

            if (chartOrderStatistics !== null) {
                const statisticsChart = new ApexCharts(chartOrderStatistics, orderChartConfig);
                statisticsChart.render();
            }
        });
        
        -->





        <!-- 
        keluar dan masuk


                    <div class="row mt-4">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="../admin/assets/img/gif-icons/barang-masuk.png" alt="chart success"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{ route('brg-masuk.index') }}">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Keseluruhan</span>
                            <small class="text-muted">total barang</small>
                            <h3 class="card-title mb-2">{{ $totalStokMasuk }}</h3>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="../admin/assets/img/gif-icons/petugas.png" alt="chart success"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{ route('brg-keluar.index') }}">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Barang Keluar</span>
                            <small class="text-muted">jumlah</small>
                            <h3 class="card-title mb-2">{{ $totalStokKeluar }}</h3>

                        </div>
                    </div>
                </div>
            </div>
        
        
        -->






        <!-- AKTITAS KESELURUHAN
         <div class="row">
        <div class="col-md-2">
            <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3 mb-2">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Total Aktivitas</h5>
                                        <span class="badge bg-label-warning rounded-pill">Keseluruhan</span>
                                    </div>
                                    <div class="mt-sm-auto d-flex gap-2">
                                        <h3 class="mb-0">{{ $total }}</h3>
                                        <small style="position: relative; top: 5px;" class="text-muted">aktivitas</small>
                                    </div>
                                </div>
                                <div id="profileReportChart"></div>
                            </div>
                            <small>Aktivitas Keseluruhan</small>
                        </div>
                    </div>
        </div>
    </div>
    
    





                const profileReportChartEl = document.querySelector('#profileReportChart'),
            profileReportChartConfig = {
                chart: {
                    height: 80,
                    // width: 175,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    dropShadow: {
                        enabled: true,
                        top: 10,
                        left: 5,
                        blur: 3,
                        color: config.colors.warning,
                        opacity: 0.15
                    },
                    sparkline: {
                        enabled: true
                    }
                },
                grid: {
                    show: false,
                    padding: {
                        right: 8
                    }
                },
                colors: [config.colors.warning],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 5,
                    curve: 'smooth'
                },
                series: [{
                    name: 'Jumlah',
                    data: {!! json_encode($chartData['series']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                    labels: {
                        style: {
                            fontSize: '10px',
                            colors: '#6B7280'
                        }
                    }
                },
                yaxis: {
                    show: false
                }
            };
        if (typeof profileReportChartEl !== undefined && profileReportChartEl !== null) {
            const profileReportChart = new ApexCharts(profileReportChartEl, profileReportChartConfig);
            profileReportChart.render();
        }


        
        -->