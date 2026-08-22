@extends('layouts.admin')
@section('page-title', 'Detail Unit Serial: ' . $item->serial_number)

@section('content')
    @include('sweetalert::alert')

    {{-- HEADER UNIT SERIAL --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xl bg-label-info rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-barcode fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="mb-0 fw-bold text-dark"><code>{{ $item->serial_number }}</code></h4>
                            @php
                                $statusBadges = [
                                    'available' => 'bg-success',
                                    'borrowed' => 'bg-warning',
                                    'in_use' => 'bg-info',
                                    'out' => 'bg-secondary',
                                    'damaged' => 'bg-danger',
                                    'lost' => 'bg-danger',
                                    'depleted' => 'bg-dark',
                                ];
                                $statusLabels = [
                                    'available' => 'Tersedia',
                                    'borrowed' => 'Sedang Dipinjam',
                                    'in_use' => 'Sedang Digunakan',
                                    'out' => 'Keluar',
                                    'damaged' => 'Rusak',
                                    'lost' => 'Hilang',
                                    'depleted' => 'Habis Terpakai',
                                ];
                            @endphp
                            <span class="badge {{ $statusBadges[$item->status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$item->status] ?? $item->status }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <span>Master Barang: <a href="{{ route('barang.show', $item->barang_id) }}" class="fw-bold text-primary">{{ $item->barang?->nama }}</a> ({{ $item->barang?->merek }})</span>
                            <span class="mx-2">•</span>
                            <span>Lokasi: <strong>{{ $item->ruangan?->nama_ruangan ?? 'Belum Ditentukan' }}</strong></span>
                            <span class="mx-2">•</span>
                            <span>Tgl Masuk: <strong>{{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTransfer">
                        <i class="bx bx-transfer me-1"></i> Pindah Lokasi
                    </button>
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalAdjust">
                        <i class="bx bx-slider-alt me-1"></i> Penyesuaian (Opname)
                    </button>
                    <a href="{{ route('inventory-item.edit', $item->id) }}" class="btn btn-outline-secondary">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>
                    <a href="{{ route('barang.show', $item->barang_id) }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Ke Master Barang
                    </a>
                </div>
            </div>

            @if ($item->keterangan)
                <div class="mt-3 p-2 bg-light rounded small text-secondary">
                    <strong>Catatan:</strong> {{ $item->keterangan }}
                </div>
            @endif
        </div>
    </div>

    {{-- SUMMARY STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-label-primary">
                <div class="card-body p-3">
                    <span class="text-primary fw-semibold small d-block mb-1">Kuantitas Awal</span>
                    <h3 class="card-title mb-0 fw-bold text-primary">
                        {{ number_format((float)$item->initial_quantity, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $item->barang?->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-label-success">
                <div class="card-body p-3">
                    <span class="text-success fw-semibold small d-block mb-1">Kuantitas Saat Ini</span>
                    <h3 class="card-title mb-0 fw-bold text-success">
                        {{ number_format((float)$item->current_quantity, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $item->barang?->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-label-secondary">
                <div class="card-body p-3">
                    <span class="text-secondary fw-semibold small d-block mb-1">Terpakai / Keluar</span>
                    @php
                        $terpakai = max(0, (float)$item->initial_quantity - (float)$item->current_quantity);
                    @endphp
                    <h3 class="card-title mb-0 fw-bold text-secondary">
                        {{ number_format($terpakai, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $item->barang?->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-label-info">
                <div class="card-body p-3">
                    <span class="text-info fw-semibold small d-block mb-1">Lokasi Terkini</span>
                    <h4 class="card-title mb-0 fw-bold text-info">
                        {{ $item->ruangan?->nama_ruangan ?? '-' }}
                    </h4>
                    <small class="text-muted">Ruangan</small>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-item-movement">
                    <i class="bx bx-history me-1"></i> Riwayat Mutasi Stok Unit Ini
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-item-location">
                    <i class="bx bx-map-pin me-1"></i> Riwayat Perpindahan Lokasi
                </button>
            </li>
        </ul>

        <div class="tab-content p-0 shadow-sm border-top-0">
            {{-- TAB 1: STOCK MOVEMENTS --}}
            <div class="tab-pane fade show active p-3" id="tab-item-movement" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Jenis Transaksi</th>
                                <th>Perubahan Qty</th>
                                <th>Saldo Sebelum</th>
                                <th>Saldo Sesudah</th>
                                <th>Lokasi</th>
                                <th>Petugas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($item->stockMovements as $mv)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $mv->tanggal?->translatedFormat('d M Y') }}</div>
                                        <small class="text-muted">{{ $mv->tanggal?->format('H:i') }} WIB</small>
                                    </td>
                                    <td>
                                        @php
                                            $typeBadges = [
                                                'in' => 'bg-label-success',
                                                'out' => 'bg-label-danger',
                                                'borrow' => 'bg-label-warning',
                                                'return' => 'bg-label-info',
                                                'transfer' => 'bg-label-primary',
                                                'adjustment' => 'bg-label-dark',
                                                'initial' => 'bg-label-secondary',
                                            ];
                                            $typeLabels = [
                                                'in' => 'Barang Masuk',
                                                'out' => 'Barang Keluar',
                                                'borrow' => 'Peminjaman',
                                                'return' => 'Pengembalian',
                                                'transfer' => 'Perpindahan Lokasi',
                                                'adjustment' => 'Penyesuaian (Opname)',
                                                'initial' => 'Saldo Awal',
                                            ];
                                        @endphp
                                        <span class="badge {{ $typeBadges[$mv->type] ?? 'bg-label-secondary' }}">
                                            {{ $typeLabels[$mv->type] ?? strtoupper($mv->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ (float)$mv->quantity > 0 ? 'text-success' : ((float)$mv->quantity < 0 ? 'text-danger' : 'text-muted') }}">
                                            {{ (float)$mv->quantity > 0 ? '+' : '' }}{{ number_format((float)$mv->quantity, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>{{ number_format((float)$mv->quantity_before, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</td>
                                    <td>{{ number_format((float)$mv->quantity_after, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</td>
                                    <td>{{ $mv->ruangan?->nama_ruangan ?? '-' }}</td>
                                    <td>{{ $mv->user?->name ?? 'Sistem' }}</td>
                                    <td><small class="text-muted">{{ $mv->keterangan ?? '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat mutasi untuk unit ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: LOCATION HISTORY --}}
            <div class="tab-pane fade p-3" id="tab-item-location" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Dari Ruangan</th>
                                <th>Ke Ruangan Tujuan</th>
                                <th>Petugas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($item->locationHistories as $lh)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $lh->tanggal?->translatedFormat('d M Y') }}</div>
                                        <small class="text-muted">{{ $lh->tanggal?->format('H:i') }} WIB</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{ $lh->fromRuangan?->nama_ruangan ?? 'Belum Ada' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $lh->toRuangan?->nama_ruangan ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $lh->user?->name ?? 'Sistem' }}</td>
                                    <td>{{ $lh->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat perpindahan lokasi untuk unit ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TRANSFER --}}
    <div class="modal fade" id="modalTransfer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('inventory-item.transfer', $item->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-transfer me-1"></i> Pindah Lokasi: {{ $item->serial_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">Lokasi Saat Ini: <strong>{{ $item->ruangan?->nama_ruangan ?? 'Belum Ada' }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Ruangan Tujuan <span class="text-danger">*</span></label>
                        <select name="to_ruangan_id" class="form-select" required>
                            <option value="">-- Pilih Ruangan Tujuan --</option>
                            @foreach ($ruangans as $r)
                                @if ($r->id != $item->ruangan_id)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Dipindahkan untuk maintenance" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Pindahkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ADJUST --}}
    <div class="modal fade" id="modalAdjust" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('inventory-item.adjust', $item->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-slider-alt me-1"></i> Penyesuaian Stok (Opname)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quantity Aktual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="any" min="0" name="new_quantity" class="form-control" value="{{ (float)$item->current_quantity }}" required />
                            <span class="input-group-text">{{ $item->barang?->unit?->symbol }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Unit</label>
                        <select name="new_status" class="form-select">
                            <option value="available" {{ $item->status === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                            <option value="in_use" {{ $item->status === 'in_use' ? 'selected' : '' }}>Sedang Digunakan (In Use)</option>
                            <option value="damaged" {{ $item->status === 'damaged' ? 'selected' : '' }}>Rusak (Damaged)</option>
                            <option value="lost" {{ $item->status === 'lost' ? 'selected' : '' }}>Hilang (Lost)</option>
                            <option value="maintenance" {{ $item->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="depleted" {{ $item->status === 'depleted' ? 'selected' : '' }}>Habis (Depleted)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penyesuaian <span class="text-danger">*</span></label>
                        <input type="text" name="alasan" class="form-control" placeholder="Contoh: Hasil stock opname" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bx bx-save me-1"></i> Simpan Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>
@endsection
