@extends('layouts.admin')
@section('page-title', 'Pusat Laporan & Ekspor Inventaris')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-4 bg-label-primary border-0">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 fw-bold text-primary"><i class="bx bx-file-blank me-2"></i>Pusat Laporan & Rekapitulasi Inventaris</h4>
                    <p class="text-muted mb-0">Pusat pencetakan dan ekspor data inventaris (PDF & Microsoft Excel) dengan parameter filter lengkap.</p>
                </div>
                <i class="bx bx-printer fs-1 text-primary"></i>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- 1. LAPORAN MASTER STOK --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-primary rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-package fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Master Stok</h6>
                            <small class="text-muted">Rekapitulasi seluruh stok barang</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Memuat data kode barang, merek, satuan, status serial, dan sisa saldo total master.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('barang.export.excel') }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('barang.export') }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. LAPORAN BUKU MUTASI / AUDIT TRAIL --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-info rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-history fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Buku Mutasi Stok</h6>
                            <small class="text-muted">Ledger mutasi & audit trail</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Memuat riwayat transaksi IN, OUT, BORROW, RETURN, TRANSFER, & ADJUSTMENT.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('stock-movement.index', ['export' => 'excel']) }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('stock-movement.index', ['export' => 'pdf']) }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. LAPORAN BARANG MASUK --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-success rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-import fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Barang Masuk</h6>
                            <small class="text-muted">Penerimaan & pengadaan</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Daftar transaksi penerimaan barang non-serial & registrasi unit serial baru.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('brg-masuk.index', ['export' => 'excel']) }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('brg-masuk.index', ['export' => 'pdf']) }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. LAPORAN BARANG KELUAR --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-danger rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-export fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Barang Keluar</h6>
                            <small class="text-muted">Pengeluaran & pemakaian</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Daftar pengeluaran barang non-serial, unit serial, dan konsumsi material kabel.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('brg-keluar.index', ['export' => 'excel']) }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('brg-keluar.index', ['export' => 'pdf']) }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. LAPORAN PEMINJAMAN --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-warning rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-calendar-event fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Peminjaman</h6>
                            <small class="text-muted">Sirkulasi peminjaman aktif</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Daftar peminjaman barang, peminjam, batas pengembalian, dan status jatuh tempo.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('peminjaman.index', ['export' => 'excel']) }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('peminjaman.index', ['export' => 'pdf']) }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. LAPORAN PENGEMBALIAN --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-success rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-undo fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Pengembalian</h6>
                            <small class="text-muted">Riwayat pengembalian barang</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Daftar pengembalian beserta catatan kondisi fisik (*Baik, Rusak, Hilang*).</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pengembalian.index', ['export' => 'excel']) }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('pengembalian.index', ['export' => 'pdf']) }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 7. LAPORAN STOK PER RUANGAN --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar avatar-md bg-label-secondary rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-building fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Laporan Stok per Ruangan</h6>
                            <small class="text-muted">Sebaran alokasi lokasi</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Daftar stok barang non-serial dan penempatan unit fisik di setiap ruangan.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('brg-ruangan.index', ['export' => 'excel']) }}" class="btn btn-sm btn-outline-success w-50">
                            <i class="bx bx-spreadsheet me-1"></i> Excel
                        </a>
                        <a href="{{ route('brg-ruangan.index', ['export' => 'pdf']) }}" class="btn btn-sm btn-outline-danger w-50">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
