@extends('layouts.admin')
@section('page-title', 'Detail Peminjaman: ' . $peminjaman->kode_barang)

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0"><i class="bx bx-calendar-event text-warning me-2"></i>Detail Transaksi Peminjaman</h5>
                <small class="text-muted">Kode Pinjam: <strong class="text-primary">{{ $peminjaman->kode_barang }}</strong></small>
            </div>
            <div class="d-flex gap-2">
                @if ($peminjaman->status === 'Sedang Dipinjam' && $outstanding > 0)
                    <a href="{{ route('pengembalian.create', ['peminjaman_id' => $peminjaman->id]) }}" class="btn btn-success">
                        <i class="bx bx-undo me-1"></i> Form Pengembalian
                    </a>
                @endif
                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                {{-- Detail Table --}}
                <div class="col-md-7">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 35%;">Kode Peminjaman</th>
                                    <td><span class="badge bg-label-primary fs-6">{{ $peminjaman->kode_barang }}</span></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Nama Peminjam</th>
                                    <td><strong class="text-dark fs-6">{{ $peminjaman->nama_peminjam }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Master Barang</th>
                                    <td>
                                        <a href="{{ route('barang.show', $peminjaman->id_barang) }}" class="fw-bold text-primary">
                                            {{ $peminjaman->barang?->nama }}
                                        </a>
                                        <div class="small text-muted">{{ $peminjaman->barang?->merek }} (Kode: {{ $peminjaman->barang?->kode_barang }})</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tipe Barang</th>
                                    <td>
                                        @if ($peminjaman->inventoryItem)
                                            <span class="badge bg-info"><i class="bx bx-barcode me-1"></i> SERIALIZED</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bx bx-cube me-1"></i> NON-SERIAL</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($peminjaman->inventoryItem)
                                    <tr>
                                        <th class="bg-light">Unit Serial Number</th>
                                        <td>
                                            <a href="{{ route('inventory-item.show', $peminjaman->inventoryItem->id) }}" class="fw-bold text-primary">
                                                <code>{{ $peminjaman->inventoryItem->serial_number }}</code>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="bg-light">Jumlah Dipinjam</th>
                                    <td>
                                        <strong class="fs-5 text-warning">{{ number_format((float)$peminjaman->jumlah, $peminjaman->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</strong>
                                        <span class="badge bg-label-dark ms-1">{{ $peminjaman->barang?->unit?->symbol ?? 'pcs' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Status Saat Ini</th>
                                    <td>
                                        @if ($peminjaman->status === 'Sedang Dipinjam')
                                            <span class="badge bg-warning fs-6">Sedang Dipinjam</span>
                                        @else
                                            <span class="badge bg-success fs-6">Sudah Dikembalikan Penuh</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tanggal Pinjam</th>
                                    <td>{{ $peminjaman->tanggal_pinjam?->translatedFormat('l, d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Batas Pengembalian</th>
                                    <td>
                                        {{ $peminjaman->tanggal_kembali?->translatedFormat('l, d F Y') }}
                                        @if ($peminjaman->status === 'Sedang Dipinjam' && \Carbon\Carbon::now()->gt($peminjaman->tanggal_kembali))
                                            <span class="badge bg-danger ms-2">Terlambat</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Petugas Pencatat</th>
                                    <td>{{ $peminjaman->user?->name ?? 'Sistem' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Status Card Summary --}}
                <div class="col-md-5">
                    <div class="card bg-label-primary border-0 p-3 mb-3">
                        <h6 class="fw-bold text-primary mb-3"><i class="bx bx-pie-chart-alt me-1"></i> Status Pengembalian Barang</h6>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Pinjam:</span>
                            <strong>{{ number_format((float)$peminjaman->jumlah, 2) }} {{ $peminjaman->barang?->unit?->symbol }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sudah Dikembalikan:</span>
                            <strong class="text-success">{{ number_format((float)$totalReturned, 2) }} {{ $peminjaman->barang?->unit?->symbol }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Belum Kembali (Outstanding):</span>
                            <strong class="text-danger fs-5">{{ number_format((float)$outstanding, 2) }} {{ $peminjaman->barang?->unit?->symbol }}</strong>
                        </div>
                    </div>

                    @if ($peminjaman->barang?->foto)
                        <div class="card p-2 text-center border">
                            <img src="{{ asset('image/barang/' . $peminjaman->barang->foto) }}" alt="{{ $peminjaman->barang->nama }}"
                                class="rounded mx-auto" style="max-height: 150px; object-fit: cover;">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Pengembalian Terkait --}}
            <h6 class="fw-bold text-dark mt-4 mb-3"><i class="bx bx-history me-1"></i> Riwayat Pengembalian Terkait Peminjaman Ini</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Kembali</th>
                            <th>Jumlah Dikembalikan</th>
                            <th>Kondisi Barang</th>
                            <th>Tanggal Kembali</th>
                            <th>Petugas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjaman->pengembalian as $ret)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('pengembalian.show', $ret->id) }}" class="fw-bold text-primary">
                                        {{ $ret->kode_barang }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">+{{ number_format((float)$ret->jumlah, 2) }}</span> {{ $peminjaman->barang?->unit?->symbol }}
                                </td>
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
                                    <span class="badge {{ $condBadges[$ret->kondisi] ?? 'bg-secondary' }}">
                                        {{ $ret->kondisi }}
                                    </span>
                                </td>
                                <td>{{ $ret->tanggal_kembali?->translatedFormat('d M Y') }}</td>
                                <td>{{ $ret->user?->name ?? 'Sistem' }}</td>
                                <td><small class="text-muted">{{ $ret->keterangan ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">Belum ada pengembalian yang dicatat untuk peminjaman ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
