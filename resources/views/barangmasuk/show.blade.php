@extends('layouts.admin')
@section('page-title', 'Detail Barang Masuk: ' . $barangMasuk->kode_barang)

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0"><i class="bx bx-import text-primary me-2"></i>Detail Transaksi Barang Masuk</h5>
                    <small class="text-muted">Kode Transaksi: <strong class="text-primary">{{ $barangMasuk->kode_barang }}</strong></small>
                </div>
                <a href="{{ route('brg-masuk.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    {{-- Detail Card --}}
                    <div class="col-md-7">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" style="width: 35%;">Kode Transaksi</th>
                                        <td><span class="badge bg-label-primary fs-6">{{ $barangMasuk->kode_barang }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Master Barang</th>
                                        <td>
                                            <a href="{{ route('barang.show', $barangMasuk->id_barang) }}" class="fw-bold text-primary">
                                                {{ $barangMasuk->barang?->nama }}
                                            </a>
                                            <div class="small text-muted">{{ $barangMasuk->barang?->merek }} (Kode: {{ $barangMasuk->barang?->kode_barang }})</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Tipe Inventaris</th>
                                        <td>
                                            @if ($barangMasuk->inventoryItem)
                                                <span class="badge bg-info"><i class="bx bx-barcode me-1"></i> SERIALIZED</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bx bx-cube me-1"></i> NON-SERIAL</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($barangMasuk->inventoryItem)
                                        <tr>
                                            <th class="bg-light">Unit Serial Number</th>
                                            <td>
                                                <a href="{{ route('inventory-item.show', $barangMasuk->inventoryItem->id) }}" class="fw-bold text-primary">
                                                    <code>{{ $barangMasuk->inventoryItem->serial_number }}</code>
                                                </a>
                                                <div class="small text-muted">Sisa Stok Unit: {{ number_format((float)$barangMasuk->inventoryItem->current_quantity, 2) }} {{ $barangMasuk->barang?->unit?->symbol }}</div>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="bg-light">Jumlah Masuk</th>
                                        <td>
                                            <span class="fw-bold fs-5 text-success">
                                                +{{ number_format((float)$barangMasuk->jumlah, $barangMasuk->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                            </span>
                                            <span class="badge bg-label-dark ms-1">{{ $barangMasuk->barang?->unit?->symbol ?? 'pcs' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Lokasi / Ruangan</th>
                                        <td>
                                            @if ($barangMasuk->ruangan)
                                                <i class="bx bx-map-pin text-primary me-1"></i>
                                                <strong>{{ $barangMasuk->ruangan->nama_ruangan }}</strong>
                                            @else
                                                <span class="text-muted">Gudang Utama (Tanpa Ruangan)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Tanggal Masuk</th>
                                        <td>{{ $barangMasuk->tanggal_masuk?->translatedFormat('l, d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Petugas Pencatat</th>
                                        <td>{{ $barangMasuk->user?->name ?? 'Sistem' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Keterangan</th>
                                        <td>{{ $barangMasuk->keterangan ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Summary Card & Image --}}
                    <div class="col-md-5">
                        <div class="card bg-light border p-3 text-center">
                            @if ($barangMasuk->barang?->foto)
                                <img src="{{ asset('image/barang/' . $barangMasuk->barang->foto) }}" alt="{{ $barangMasuk->barang->nama }}"
                                    class="rounded shadow-sm mx-auto mb-3" style="max-height: 180px; object-fit: cover;">
                            @else
                                <div class="avatar avatar-xl bg-label-primary mx-auto my-3 rounded d-flex align-items-center justify-content-center">
                                    <i class="bx bx-package fs-1"></i>
                                </div>
                            @endif
                            <h6 class="fw-bold mb-1">{{ $barangMasuk->barang?->nama }}</h6>
                            <p class="text-muted small mb-2">{{ $barangMasuk->barang?->merek }}</p>
                            <a href="{{ route('barang.show', $barangMasuk->id_barang) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show me-1"></i> Buka Master Barang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
