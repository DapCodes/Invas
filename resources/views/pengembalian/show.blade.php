@extends('layouts.admin')
@section('page-title', 'Detail Pengembalian: ' . $pengembalian->kode_barang)

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0"><i class="bx bx-undo text-success me-2"></i>Detail Transaksi Pengembalian Barang</h5>
                    <small class="text-muted">Kode Transaksi: <strong class="text-success">{{ $pengembalian->kode_barang }}</strong></small>
                </div>
                <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" style="width: 35%;">Kode Pengembalian</th>
                                        <td><span class="badge bg-label-success fs-6">{{ $pengembalian->kode_barang }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Peminjam</th>
                                        <td><strong class="text-dark fs-6">{{ $pengembalian->nama_peminjam }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Referensi Peminjaman</th>
                                        <td>
                                            @if ($pengembalian->peminjamans)
                                                <a href="{{ route('peminjaman.show', $pengembalian->id_peminjam) }}" class="fw-bold text-primary">
                                                    #{{ $pengembalian->peminjamans->kode_barang }}
                                                </a>
                                                <span class="badge bg-label-info ms-2">Status: {{ $pengembalian->peminjamans->status }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Master Barang</th>
                                        <td>
                                            <a href="{{ route('barang.show', $pengembalian->id_barang) }}" class="fw-bold text-primary">
                                                {{ $pengembalian->barang?->nama }}
                                            </a>
                                            <div class="small text-muted">{{ $pengembalian->barang?->merek }}</div>
                                        </td>
                                    </tr>
                                    @if ($pengembalian->inventoryItem)
                                        <tr>
                                            <th class="bg-light">Unit Serial Number</th>
                                            <td>
                                                <a href="{{ route('inventory-item.show', $pengembalian->inventoryItem->id) }}" class="fw-bold text-primary">
                                                    <code>{{ $pengembalian->inventoryItem->serial_number }}</code>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="bg-light">Jumlah Dikembalikan</th>
                                        <td>
                                            <strong class="fs-5 text-success">+{{ number_format((float)$pengembalian->jumlah, $pengembalian->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</strong>
                                            <span class="badge bg-label-dark ms-1">{{ $pengembalian->barang?->unit?->symbol ?? 'pcs' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Kondisi Barang</th>
                                        <td>
                                            @php
                                                $condBadges = [
                                                    'Baik' => 'bg-success',
                                                    'Rusak' => 'bg-danger',
                                                    'Sebagian Rusak' => 'bg-warning',
                                                    'Hilang' => 'bg-dark',
                                                    'Tidak Lengkap' => 'bg-secondary',
                                                ];
                                            @endphp
                                            <span class="badge {{ $condBadges[$pengembalian->kondisi] ?? 'bg-secondary' }} fs-6">
                                                {{ $pengembalian->kondisi }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Ruangan Penyimpanan</th>
                                        <td>
                                            @if ($pengembalian->ruangan)
                                                <i class="bx bx-map-pin text-primary me-1"></i>{{ $pengembalian->ruangan->nama_ruangan }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Tanggal Dikembalikan</th>
                                        <td>{{ $pengembalian->tanggal_kembali?->translatedFormat('l, d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Petugas Penerima</th>
                                        <td>{{ $pengembalian->user?->name ?? 'Sistem' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Keterangan / Catatan</th>
                                        <td>{{ $pengembalian->keterangan ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card bg-light border p-3 text-center">
                            @if ($pengembalian->barang?->foto)
                                <img src="{{ asset('image/barang/' . $pengembalian->barang->foto) }}" alt="{{ $pengembalian->barang->nama }}"
                                    class="rounded shadow-sm mx-auto mb-3" style="max-height: 180px; object-fit: cover;">
                            @else
                                <div class="avatar avatar-xl bg-label-success mx-auto my-3 rounded d-flex align-items-center justify-content-center">
                                    <i class="bx bx-package fs-1"></i>
                                </div>
                            @endif
                            <h6 class="fw-bold mb-1">{{ $pengembalian->barang?->nama }}</h6>
                            <p class="text-muted small mb-2">{{ $pengembalian->barang?->merek }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
