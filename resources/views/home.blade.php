@extends('layouts.admin')
@section('page-title', 'Beranda')

@section('content')
<style>
    /* Home App Launcher Custom Styling */
    .launcher-header {
        margin-bottom: 1.25rem;
    }

    .launcher-greeting {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin-bottom: 0.25rem;
    }

    .launcher-subtitle {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0;
    }

    /* Minimal Search Bar */
    .launcher-search-box {
        max-width: 520px;
        position: relative;
        margin-bottom: 1.5rem;
    }

    .launcher-search-input {
        width: 100%;
        padding: 0.65rem 2.5rem 0.65rem 2.5rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.88rem;
        color: #0f172a;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .launcher-search-input:focus {
        outline: none;
        border-color: var(--invas-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .launcher-search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.15rem;
        color: #94a3b8;
    }

    .launcher-search-badge {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        padding: 1px 6px;
        background: #f1f5f9;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
    }

    /* Category Group Title */
    .launcher-category-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
    }

    .launcher-category-title {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0;
    }

    /* App Launcher Grid Item */
    .launcher-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.15rem 0.85rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        color: #0f172a;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .launcher-tile:hover {
        transform: translateY(-3px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 18px -4px rgba(15, 23, 42, 0.06);
        color: var(--invas-primary);
    }

    .launcher-tile:active {
        transform: scale(0.97);
    }

    .launcher-tile-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        margin-bottom: 0.65rem;
        transition: all 0.2s ease;
    }

    .launcher-tile-name {
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.25;
        color: #0f172a;
    }

    .launcher-tile:hover .launcher-tile-name {
        color: var(--invas-primary);
    }

    /* Summary Mini Cards */
    .recent-summary-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .recent-mini-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        color: #334155;
        transition: all 0.18s ease;
    }

    .recent-mini-card:hover {
        border-color: #cbd5e1;
        color: var(--invas-primary);
        background: #f8fafc;
    }

    @media (max-width: 767.98px) {
        .launcher-tile {
            padding: 0.9rem 0.5rem;
            border-radius: 14px;
        }

        .launcher-tile-icon {
            width: 38px;
            height: 38px;
            font-size: 1.25rem;
            margin-bottom: 0.4rem;
        }

        .launcher-tile-name {
            font-size: 0.78rem;
        }
    }
</style>

{{-- Header Greeting --}}
<div class="launcher-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="launcher-greeting">Selamat Datang, {{ Auth::user()->name }}</h4>
            <p class="launcher-subtitle">Sistem Manajemen Logistik, Inventaris Multi-Stok, Serial Number & Perpindahan Ruangan</p>
        </div>
        <span class="badge bg-label-primary font-monospace small d-none d-md-inline-block px-3 py-2">
            <i class="bx bx-shield-quarter me-1"></i> {{ Auth::user()->is_admin ? 'ADMINISTRATOR' : 'PETUGAS LOGISTIK' }}
        </span>
    </div>
</div>

{{-- Instant Filter Search Bar --}}
<div class="launcher-search-box">
    <i class="bx bx-search launcher-search-icon"></i>
    <input type="text" 
           id="launcherSearchInput" 
           class="launcher-search-input" 
           placeholder="Cari seluruh menu & fitur sistem..." 
           autocomplete="off" />
    <span class="launcher-search-badge">/</span>
</div>

<div id="launcherGridContainer">
    {{-- CATEGORY 1: TRANSAKSI & SIRKULASI --}}
    <div class="launcher-category-header">
        <i class="bx bx-transfer-alt text-primary fs-5"></i>
        <h6 class="launcher-category-title">TRANSAKSI & SIRKULASI</h6>
        <hr class="flex-grow-1 my-0 opacity-25">
    </div>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-2 g-md-3 mb-3">
        {{-- Pinjam Barang --}}
        <div class="col launcher-item" data-keywords="pinjam peminjaman barang transaksi alat peminjam">
            <a href="{{ route('peminjaman.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-warning text-warning">
                    <i class="bx bx-calendar-event"></i>
                </div>
                <div class="launcher-tile-name">Peminjaman</div>
            </a>
        </div>

        {{-- Pengembalian --}}
        <div class="col launcher-item" data-keywords="pengembalian kembali barang selesai riwayat kondisi fisik">
            <a href="{{ route('pengembalian.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-info text-info">
                    <i class="bx bx-undo"></i>
                </div>
                <div class="launcher-tile-name">Pengembalian</div>
            </a>
        </div>

        {{-- Barang Masuk --}}
        <div class="col launcher-item" data-keywords="barang masuk penerimaan pengadaan mutasi tambah stok">
            <a href="{{ route('brg-masuk.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-success text-success">
                    <i class="bx bx-import"></i>
                </div>
                <div class="launcher-tile-name">Barang Masuk</div>
            </a>
        </div>

        {{-- Barang Keluar --}}
        <div class="col launcher-item" data-keywords="barang keluar pengeluaran mutasi distribusi kurangi stok pemakaian kabel">
            <a href="{{ route('brg-keluar.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-danger text-danger">
                    <i class="bx bx-export"></i>
                </div>
                <div class="launcher-tile-name">Barang Keluar</div>
            </a>
        </div>
    </div>

    {{-- CATEGORY 2: INVENTORY & KONTROL STOK --}}
    <div class="launcher-category-header">
        <i class="bx bx-box text-primary fs-5"></i>
        <h6 class="launcher-category-title">INVENTORY & KONTROL STOK</h6>
        <hr class="flex-grow-1 my-0 opacity-25">
    </div>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-2 g-md-3 mb-3">
        {{-- Master Barang --}}
        <div class="col launcher-item" data-keywords="data barang master inventori produk stok sku non serial">
            <a href="{{ route('barang.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-primary text-primary">
                    <i class="bx bx-package"></i>
                </div>
                <div class="launcher-tile-name">Data Master Barang</div>
            </a>
        </div>

        {{-- Unit Serial Number --}}
        <div class="col launcher-item" data-keywords="serial number unit barcode kabel fiber iphone roll meter">
            <a href="{{ route('inventory-item.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-info text-info">
                    <i class="bx bx-barcode"></i>
                </div>
                <div class="launcher-tile-name">Unit Serial Number</div>
            </a>
        </div>

        {{-- Buku Mutasi & Audit --}}
        <div class="col launcher-item" data-keywords="buku mutasi ledger audit history pergerakan stok log rekam jejak in out">
            <a href="{{ route('stock-movement.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-secondary text-secondary">
                    <i class="bx bx-history"></i>
                </div>
                <div class="launcher-tile-name">Buku Mutasi & Audit</div>
            </a>
        </div>

        {{-- Koreksi Stok (Opname) --}}
        <div class="col launcher-item" data-keywords="koreksi stok opname penyesuaian selisih adjustment fisik sistem">
            <a href="{{ route('stock-adjustment.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-dark text-dark">
                    <i class="bx bx-slider-alt"></i>
                </div>
                <div class="launcher-tile-name">Koreksi Stok (Opname)</div>
            </a>
        </div>
    </div>

    {{-- CATEGORY 3: MASTER DATA, LAPORAN & SISTEM --}}
    <div class="launcher-category-header">
        <i class="bx bx-folder text-primary fs-5"></i>
        <h6 class="launcher-category-title">MASTER DATA, LAPORAN & SISTEM</h6>
        <hr class="flex-grow-1 my-0 opacity-25">
    </div>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3 mb-4">
        {{-- Transfer Stok Ruangan --}}
        <div class="col launcher-item" data-keywords="barang ruangan stok sebaran distribusi lokasi transfer pindah ruang">
            <a href="{{ route('brg-ruangan.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-primary text-primary">
                    <i class="bx bx-transfer-alt"></i>
                </div>
                <div class="launcher-tile-name">Transfer Stok Ruangan</div>
            </a>
        </div>

        {{-- Data Ruangan (Admin) --}}
        @if (Auth::user()->is_admin == 1)
            <div class="col launcher-item" data-keywords="data ruangan kelas bengkel laboratorium lab lokasi gedung tempat">
                <a href="{{ route('ruangan.index') }}" class="launcher-tile">
                    <div class="launcher-tile-icon bg-label-secondary text-secondary">
                        <i class="bx bx-map-pin"></i>
                    </div>
                    <div class="launcher-tile-name">Data Ruangan</div>
                </a>
            </div>
        @endif

        {{-- Data Vendor --}}
        <div class="col launcher-item" data-keywords="vendor supplier penyedia rekanan toko distributor pihak ketiga">
            <a href="{{ route('vendor.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-info text-info">
                    <i class="bx bx-store"></i>
                </div>
                <div class="launcher-tile-name">Data Vendor</div>
            </a>
        </div>

        {{-- Pusat Laporan --}}
        <div class="col launcher-item" data-keywords="laporan rekap cetak pdf excel print report pusat export unduh">
            <a href="{{ route('reports.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-danger text-danger">
                    <i class="bx bx-file-blank"></i>
                </div>
                <div class="launcher-tile-name">Pusat Laporan</div>
            </a>
        </div>

        {{-- Statistik & Analitik --}}
        <div class="col launcher-item" data-keywords="statistik laporan grafik analitik diagram report chart tren">
            <a href="{{ route('admin.statistik') }}" class="launcher-tile">
                <div class="launcher-tile-icon bg-label-success text-success">
                    <i class="bx bx-pie-chart-alt-2"></i>
                </div>
                <div class="launcher-tile-name">Statistik</div>
            </a>
        </div>

        {{-- Data Petugas (Admin) --}}
        @if (Auth::user()->is_admin == 1)
            <div class="col launcher-item" data-keywords="petugas karyawan user admin akun staf pengguna kelola auth">
                <a href="{{ route('karyawan.index') }}" class="launcher-tile">
                    <div class="launcher-tile-icon bg-label-warning text-warning">
                        <i class="bx bx-user-check"></i>
                    </div>
                    <div class="launcher-tile-name">Data Petugas</div>
                </a>
            </div>
        @endif
    </div>
</div>

{{-- No Results Alert --}}
<div id="noResultsMsg" class="alert alert-light border text-center p-3 d-none">
    <i class="bx bx-search-alt fs-2 text-muted mb-2 d-block"></i>
    <span class="text-muted fw-semibold">Menu tidak ditemukan. Coba gunakan kata kunci pencarian lainnya.</span>
</div>

{{-- Metric Ringkasan Status Inventaris --}}
@php
    $recentMetrics = [
        ['label' => 'Total Master SKU', 'count' => $barang, 'route' => 'barang.index', 'icon' => 'bx-package', 'color' => 'primary'],
        ['label' => 'Total Unit Serial', 'count' => $totalSerialUnits ?? 0, 'route' => 'inventory-item.index', 'icon' => 'bx-barcode', 'color' => 'info'],
        ['label' => 'Pinjam Aktif', 'count' => $peminjaman, 'route' => 'peminjaman.index', 'icon' => 'bx-calendar-event', 'color' => 'warning'],
        ['label' => 'Jatuh Tempo / Telat', 'count' => $overdueLoansCount ?? 0, 'route' => 'peminjaman.index', 'icon' => 'bx-error-circle', 'color' => 'danger'],
    ];
@endphp

<div class="recent-summary-section">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="launcher-category-title mb-0">
            <span>RINGKASAN STATUS INVENTARIS</span>
        </div>
    </div>

    <div class="row g-2">
        @foreach ($recentMetrics as $metric)
            <div class="col-6 col-md-3">
                <a href="{{ route($metric['route']) }}" class="recent-mini-card">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx {{ $metric['icon'] }} text-{{ $metric['color'] }} fs-5"></i>
                        <span class="small fw-semibold text-dark">{{ $metric['label'] }}</span>
                    </div>
                    <span class="badge bg-label-{{ $metric['color'] }} rounded-pill">{{ $metric['count'] }}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>

{{-- Quick Filter Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('launcherSearchInput');
        const items = document.querySelectorAll('.launcher-item');
        const noResults = document.getElementById('noResultsMsg');
        const headers = document.querySelectorAll('.launcher-category-header');

        if (searchInput) {
            document.addEventListener('keydown', function (e) {
                if (e.key === '/' && document.activeElement !== searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                let matched = 0;

                items.forEach(function (item) {
                    const name = item.querySelector('.launcher-tile-name').textContent.toLowerCase();
                    const keywords = item.getAttribute('data-keywords') || '';

                    if (name.includes(query) || keywords.includes(query)) {
                        item.style.display = '';
                        matched++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Toggle headers visibility when searching
                if (query.length > 0) {
                    headers.forEach(h => h.style.display = 'none');
                } else {
                    headers.forEach(h => h.style.display = '');
                }

                if (matched === 0 && query.length > 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            });
        }
    });
</script>
@endsection