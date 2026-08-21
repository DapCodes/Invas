@extends('layouts.admin')
@section('page-title', 'Data Vendor / Detail')

@section('content')
    @include('sweetalert::alert')

    {{-- Main Header Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1 text-primary fw-bold">{{ $vendor->name }}</h4>
                <div class="text-muted">
                    <i class="bx bx-barcode me-1"></i> {{ $vendor->code ?? '-' }}
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('vendor.edit', $vendor->id) }}" class="btn btn-warning">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                </a>
                <a href="{{ route('vendor.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Vendor Information Grid --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1 text-muted">
                        <i class="bx bx-phone me-1"></i> Telepon
                    </span>
                    <h6 class="card-title mb-0 fs-6">{{ $vendor->phone ?? '-' }}</h6>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1 text-muted">
                        <i class="bx bx-envelope me-1"></i> Email
                    </span>
                    <h6 class="card-title mb-0 fs-6">{{ $vendor->email ?? '-' }}</h6>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1 text-muted">
                        <i class="bx bx-package me-1"></i> Jumlah Barang
                    </span>
                    <h4 class="card-title mb-0 text-primary fw-bold">{{ $vendor->barangs_count ?? $vendor->barangs->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="fw-semibold d-block mb-1 text-muted">
                        <i class="bx bx-calendar me-1"></i> Tanggal Terdaftar
                    </span>
                    <h6 class="card-title mb-0 fs-6">
                        {{ $vendor->created_at ? \Carbon\Carbon::parse($vendor->created_at)->translatedFormat('d F Y') : '-' }}
                    </h6>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="bx bx-map me-1 text-primary"></i> Alamat</h6>
                            <p class="mb-0 text-secondary">{{ $vendor->address ?? 'Tidak ada alamat' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="bx bx-detail me-1 text-primary"></i> Deskripsi / Keterangan</h6>
                            <p class="mb-0 text-secondary">{{ $vendor->description ?? 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Barang dari Vendor Ini --}}
    <div class="card mb-4">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bx bx-box me-2 text-primary"></i> Barang dari Vendor Ini
            </h5>
            <span class="badge bg-label-primary">{{ $vendor->barangs->count() }} Items</span>
        </div>
        <div class="card-body p-0">
            @if ($vendor->barangs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bx bx-package fs-1 d-block mb-2 text-secondary"></i>
                    <p class="mb-0 fs-6">Belum ada barang dari vendor ini.</p>
                </div>
            @else
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Merek</th>
                                <th>Nomor Seri</th>
                                <th>Foto</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vendor->barangs as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold">{{ $item->kode_barang }}</span></td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->merek }}</td>
                                    <td>
                                        @if ($item->serial_number)
                                            <span class="badge bg-label-secondary"><i class="bx bx-barcode me-1"></i>{{ $item->serial_number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->foto)
                                            <a href="{{ asset('image/barang/' . $item->foto) }}" target="_blank">
                                                <img style="width: 45px; height: 45px; object-fit: cover; border-radius: 5px; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.1);"
                                                    src="{{ asset('/image/barang/' . $item->foto) }}" alt="{{ $item->nama }}">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-label-info">{{ $item->stok }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('barang.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-show me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
