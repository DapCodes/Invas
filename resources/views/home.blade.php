@extends('layouts.admin')
@section('page-title', 'Beranda')

@section('content')
<style>
    /* Home App Launcher Custom Styling */
    .launcher-header {
        margin-bottom: 1.5rem;
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
        max-width: 480px;
        position: relative;
        margin-bottom: 1.75rem;
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

    /* Section Label */
    .launcher-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 0.85rem;
    }

    /* App Launcher Grid Item */
    .launcher-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem 0.85rem;
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
        width: 48px;
        height: 48px;
        background: #eff6ff;
        color: var(--invas-primary);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .launcher-tile:hover .launcher-tile-icon {
        background: var(--invas-primary);
        color: #ffffff;
    }

    .launcher-tile-name {
        font-size: 0.88rem;
        font-weight: 600;
        line-height: 1.25;
        color: #0f172a;
    }

    .launcher-tile:hover .launcher-tile-name {
        color: var(--invas-primary);
    }

    /* Recent Notification Section */
    .recent-summary-section {
        margin-top: 2.25rem;
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
            padding: 1rem 0.65rem;
            border-radius: 14px;
        }

        .launcher-tile-icon {
            width: 42px;
            height: 42px;
            font-size: 1.35rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
        }

        .launcher-tile-name {
            font-size: 0.8rem;
        }
    }
</style>

{{-- Header Greeting --}}
<div class="launcher-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="launcher-greeting">Selamat Datang, {{ Auth::user()->name }}</h4>
            <p class="launcher-subtitle">Pilih menu layanan logistik yang ingin digunakan</p>
        </div>
        <span class="badge bg-label-primary font-monospace small d-none d-md-inline-block">
            {{ Auth::user()->is_admin ? 'ADMIN' : 'PETUGAS' }}
        </span>
    </div>
</div>

{{-- Instant Filter Search Bar --}}
<div class="launcher-search-box">
    <i class="bx bx-search launcher-search-icon"></i>
    <input type="text" 
           id="launcherSearchInput" 
           class="launcher-search-input" 
           placeholder="Cari menu fitur..." 
           autocomplete="off" />
    <span class="launcher-search-badge">/</span>
</div>

{{-- Section Title --}}
<div class="launcher-section-title">
    <span>MENU APLIKASI</span>
</div>

{{-- App Launcher Grid (1 MENU = 1 KOTAK) --}}
<div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3 mb-4" id="launcherGrid">
    {{-- 1. Pinjam Barang (Prioritas Utama) --}}
    <div class="col launcher-item" data-keywords="pinjam peminjaman barang transaksi alat">
        <a href="{{ route('peminjaman.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-upload"></i>
            </div>
            <div class="launcher-tile-name">Pinjam Barang</div>
        </a>
    </div>

    {{-- 2. Data Barang --}}
    <div class="col launcher-item" data-keywords="data barang master inventori produk stok">
        <a href="{{ route('barang.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-package"></i>
            </div>
            <div class="launcher-tile-name">Data Barang</div>
        </a>
    </div>

    {{-- 3. Barang Masuk --}}
    <div class="col launcher-item" data-keywords="barang masuk pengadaan mutasi tambah stok">
        <a href="{{ route('brg-masuk.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-log-in-circle"></i>
            </div>
            <div class="launcher-tile-name">Barang Masuk</div>
        </a>
    </div>

    {{-- 4. Barang Keluar --}}
    <div class="col launcher-item" data-keywords="barang keluar mutasi distribusi kurangi stok">
        <a href="{{ route('brg-keluar.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-log-out-circle"></i>
            </div>
            <div class="launcher-tile-name">Barang Keluar</div>
        </a>
    </div>

    {{-- 5. Pengembalian --}}
    <div class="col launcher-item" data-keywords="pengembalian kembali barang selesai riwayat">
        <a href="{{ route('pengembalian.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-history"></i>
            </div>
            <div class="launcher-tile-name">Pengembalian</div>
        </a>
    </div>

    {{-- 6. Barang Ruangan --}}
    <div class="col launcher-item" data-keywords="barang ruangan stok sebaran distribusi lokasi">
        <a href="{{ route('brg-ruangan.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-box"></i>
            </div>
            <div class="launcher-tile-name">Barang Ruangan</div>
        </a>
    </div>

    {{-- 7. Data Vendor --}}
    <div class="col launcher-item" data-keywords="vendor supplier penyedia rekanan toko">
        <a href="{{ route('vendor.index') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-store"></i>
            </div>
            <div class="launcher-tile-name">Data Vendor</div>
        </a>
    </div>

    {{-- 8. Data Ruangan (Admin) --}}
    @if (Auth::user()->is_admin == 1)
        <div class="col launcher-item" data-keywords="data ruangan kelas bengkel laboratorium lab lokasi">
            <a href="{{ route('ruangan.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon">
                    <i class="bx bx-building-house"></i>
                </div>
                <div class="launcher-tile-name">Data Ruangan</div>
            </a>
        </div>
    @endif

    {{-- 9. Data Petugas (Admin) --}}
    @if (Auth::user()->is_admin == 1)
        <div class="col launcher-item" data-keywords="petugas karyawan user admin akun staf pengguna">
            <a href="{{ route('karyawan.index') }}" class="launcher-tile">
                <div class="launcher-tile-icon">
                    <i class="bx bx-id-card"></i>
                </div>
                <div class="launcher-tile-name">Data Petugas</div>
            </a>
        </div>
    @endif

    {{-- 10. Statistik & Laporan --}}
    <div class="col launcher-item" data-keywords="statistik laporan grafik analitik diagram report">
        <a href="{{ route('admin.statistik') }}" class="launcher-tile">
            <div class="launcher-tile-icon">
                <i class="bx bx-line-chart"></i>
            </div>
            <div class="launcher-tile-name">Statistik</div>
        </a>
    </div>
</div>

{{-- No Results Alert --}}
<div id="noResultsMsg" class="alert alert-light border text-center p-3 d-none">
    <small class="text-muted">Menu tidak ditemukan. Coba cari dengan kata kunci lain.</small>
</div>

{{-- Notifikasi / Data Terbaru 1 Minggu Terakhir --}}
@php
    use Carbon\Carbon;
    $startDate = Carbon::now()->subDays(7)->translatedFormat('d F Y');
    $endDate = Carbon::now()->translatedFormat('d F Y');

    $recentMetrics = [
        ['label' => 'Barang Masuk', 'count' => $barangMasuk, 'route' => 'brg-masuk.index', 'icon' => 'bx-log-in-circle'],
        ['label' => 'Barang Keluar', 'count' => $barangKeluar, 'route' => 'brg-keluar.index', 'icon' => 'bx-log-out-circle'],
        ['label' => 'Peminjaman', 'count' => $peminjaman, 'route' => 'peminjaman.index', 'icon' => 'bx-upload'],
        ['label' => 'Pengembalian', 'count' => $pengembalian, 'route' => 'pengembalian.index', 'icon' => 'bx-history'],
    ];
@endphp

<div class="recent-summary-section">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="launcher-section-title mb-0">
            <span>DATA TERBARU 1 MINGGU TERAKHIR</span>
        </div>
        <small class="text-muted font-monospace" style="font-size: 0.7rem;">{{ $startDate }} - {{ $endDate }}</small>
    </div>

    <div class="row g-2">
        @foreach ($recentMetrics as $metric)
            <div class="col-6 col-md-3">
                <a href="{{ route($metric['route']) }}" class="recent-mini-card">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx {{ $metric['icon'] }} text-muted fs-5"></i>
                        <span class="small fw-semibold text-dark">{{ $metric['label'] }}</span>
                    </div>
                    <span class="badge bg-label-{{ $metric['count'] > 0 ? 'primary' : 'secondary' }} rounded-pill">{{ $metric['count'] }}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>

@if (session('success_login'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast show bg-white shadow-sm border rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header border-bottom py-2">
                <strong class="me-auto text-dark small">INVAS</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-dark py-2 small">
                {{ session('success_login') }}
            </div>
        </div>
    </div>
@endif

{{-- Quick Filter Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('launcherSearchInput');
        const items = document.querySelectorAll('.launcher-item');
        const noResults = document.getElementById('noResultsMsg');

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