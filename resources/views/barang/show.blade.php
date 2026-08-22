@extends('layouts.admin')
@section('page-title', 'Detail Master Barang: ' . $barang->nama)

@section('content')
    @include('sweetalert::alert')

    {{-- HEADER MASTER BARANG --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div class="d-flex align-items-center gap-3">
                    @if ($barang->foto)
                        <img src="{{ asset('image/barang/' . $barang->foto) }}" alt="{{ $barang->nama }}"
                            class="rounded border shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="avatar avatar-xl bg-label-primary rounded d-flex align-items-center justify-content-center">
                            <i class="bx bx-package fs-1"></i>
                        </div>
                    @endif
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-label-primary fw-bold">{{ $barang->kode_barang }}</span>
                            @if ($barang->has_serial_number)
                                <span class="badge bg-info"><i class="bx bx-barcode me-1"></i> SERIALIZED</span>
                            @else
                                <span class="badge bg-secondary"><i class="bx bx-cube me-1"></i> NON-SERIALIZED</span>
                            @endif
                            @if ($barang->is_active)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-danger">Nonaktif</span>
                            @endif
                        </div>
                        <h4 class="mb-1 fw-bold text-dark">{{ $barang->nama }}</h4>
                        <div class="text-muted small">
                            <span><i class="bx bx-buildings me-1"></i> Merek: <strong>{{ $barang->merek }}</strong></span>
                            <span class="mx-2">•</span>
                            <span><i class="bx bx-store me-1"></i> Vendor: <strong>{{ $barang->vendor?->name ?? 'Tidak Ada' }}</strong></span>
                            <span class="mx-2">•</span>
                            <span><i class="bx bx-ruler me-1"></i> Satuan: <strong>{{ $barang->unit?->name ?? 'Pcs' }} ({{ $barang->unit?->symbol ?? 'pcs' }})</strong></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if ($barang->has_serial_number)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSerial">
                            <i class="bx bx-plus me-1"></i> Tambah Unit Serial
                        </button>
                    @endif
                    <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-outline-warning">
                        <i class="bx bx-edit-alt me-1"></i> Edit Barang
                    </a>
                    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>

            @if ($barang->deskripsi)
                <div class="mt-3 p-2 bg-light rounded small text-secondary">
                    <strong>Deskripsi:</strong> {{ $barang->deskripsi }}
                </div>
            @endif
        </div>
    </div>

    {{-- DASHBOARD SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 border-0 shadow-sm bg-label-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-primary fw-semibold small">Total Stok</span>
                        <i class="bx bx-archive fs-4 text-primary"></i>
                    </div>
                    <h3 class="card-title mb-0 fw-bold text-primary">
                        {{ number_format($summary['total_stock'], $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $barang->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 border-0 shadow-sm bg-label-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-success fw-semibold small">Tersedia</span>
                        <i class="bx bx-check-circle fs-4 text-success"></i>
                    </div>
                    <h3 class="card-title mb-0 fw-bold text-success">
                        {{ number_format($summary['available_stock'], $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $barang->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 border-0 shadow-sm bg-label-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-warning fw-semibold small">Dipinjam</span>
                        <i class="bx bx-time-five fs-4 text-warning"></i>
                    </div>
                    <h3 class="card-title mb-0 fw-bold text-warning">
                        {{ number_format($summary['borrowed_stock'], $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $barang->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 border-0 shadow-sm bg-label-secondary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary fw-semibold small">Keluar / Terpakai</span>
                        <i class="bx bx-export fs-4 text-secondary"></i>
                    </div>
                    <h3 class="card-title mb-0 fw-bold text-secondary">
                        {{ number_format($summary['out_stock'], $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">{{ $barang->unit?->symbol }}</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 border-0 shadow-sm bg-label-info">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-info fw-semibold small">Total Serial</span>
                        <i class="bx bx-barcode fs-4 text-info"></i>
                    </div>
                    <h3 class="card-title mb-0 fw-bold text-info">
                        {{ $summary['total_serials'] }}
                    </h3>
                    <small class="text-muted">Unit / Drum</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 border-0 shadow-sm bg-label-dark">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-dark fw-semibold small">Sebaran Lokasi</span>
                        <i class="bx bx-map-pin fs-4 text-dark"></i>
                    </div>
                    <h3 class="card-title mb-0 fw-bold text-dark">
                        {{ $summary['total_locations'] }}
                    </h3>
                    <small class="text-muted">Ruangan Aktif</small>
                </div>
            </div>
        </div>
    </div>

    {{-- NAV TABS --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-inventory-units">
                    @if ($barang->has_serial_number)
                        <i class="bx bx-barcode me-1"></i> Daftar Unit Serial Number
                    @else
                        <i class="bx bx-building me-1"></i> Stok per Ruangan
                    @endif
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-stock-movement">
                    <i class="bx bx-history me-1"></i> Buku Mutasi Stok (Stock Movement Ledger)
                </button>
            </li>
        </ul>

        <div class="tab-content p-0 shadow-sm border-top-0">
            {{-- TAB 1: INVENTORY ITEMS / ROOM STOCKS --}}
            <div class="tab-pane fade show active p-3" id="tab-inventory-units" role="tabpanel">
                @if ($barang->has_serial_number)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold">Unit Fisik & Serial Terdaftar</h6>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSerial">
                            <i class="bx bx-plus me-1"></i> Tambah Serial
                        </button>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Nomor Seri</th>
                                    <th>Sisa Quantity</th>
                                    <th>Kuantitas Awal</th>
                                    <th>Status</th>
                                    <th>Lokasi / Ruangan</th>
                                    <th>Tanggal Masuk</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inventoryItems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration + ($inventoryItems->firstItem() - 1) }}</td>
                                        <td>
                                            <a href="{{ route('inventory-item.show', $item->id) }}" class="fw-bold text-primary">
                                                <code>{{ $item->serial_number }}</code>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-bold fs-6 {{ (float)$item->current_quantity > 0 ? 'text-success' : 'text-muted' }}">
                                                {{ number_format((float)$item->current_quantity, $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                            </span>
                                            <span class="badge bg-label-secondary ms-1">{{ $barang->unit?->symbol }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ number_format((float)$item->initial_quantity, $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadges = [
                                                    'available' => 'bg-label-success',
                                                    'borrowed' => 'bg-label-warning',
                                                    'in_use' => 'bg-label-info',
                                                    'out' => 'bg-label-secondary',
                                                    'damaged' => 'bg-label-danger',
                                                    'lost' => 'bg-label-danger',
                                                    'depleted' => 'bg-label-dark',
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
                                            <span class="badge {{ $statusBadges[$item->status] ?? 'bg-label-secondary' }}">
                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->ruangan)
                                                <i class="bx bx-map-pin text-primary me-1"></i>{{ $item->ruangan->nama_ruangan }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('inventory-item.show', $item->id) }}">
                                                        <i class="bx bx-show me-1 text-info"></i> Detail & Riwayat
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="openTransferModal({{ $item->id }}, '{{ $item->serial_number }}', '{{ $item->ruangan?->nama_ruangan ?? 'Belum ada' }}')">
                                                        <i class="bx bx-transfer me-1 text-primary"></i> Pindah Lokasi
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="openAdjustModal({{ $item->id }}, '{{ $item->serial_number }}', {{ $item->current_quantity }}, '{{ $item->status }}')">
                                                        <i class="bx bx-slider-alt me-1 text-warning"></i> Penyesuaian (Opname)
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('inventory-item.edit', $item->id) }}">
                                                        <i class="bx bx-edit-alt me-1 text-secondary"></i> Edit Serial
                                                    </a>
                                                    <form id="form-delete-item-{{ $item->id }}" action="{{ route('inventory-item.destroy', $item->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <a href="#" class="dropdown-item text-danger" onclick="confirmDeleteItem({{ $item->id }})">
                                                        <i class="bx bx-trash me-1"></i> Hapus / Nonaktif
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            Belum ada unit serial terdaftar untuk barang ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $inventoryItems->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @else
                    {{-- NON SERIAL: STOCK PER RUANGAN --}}
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Ruangan / Lokasi</th>
                                    <th>Stok Tersedia</th>
                                    <th>Keterangan Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roomStocks as $roomStock)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <i class="bx bx-map-pin text-primary me-1"></i>
                                            <strong>{{ $roomStock->ruangan?->nama_ruangan }}</strong>
                                        </td>
                                        <td>
                                            <span class="fw-bold fs-6 text-success">
                                                {{ number_format((float)$roomStock->stok, $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                            </span>
                                            <span class="badge bg-label-secondary ms-1">{{ $barang->unit?->symbol }}</span>
                                        </td>
                                        <td>{{ $roomStock->ruangan?->deskripsi ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            Stok belum teralokasi ke ruangan manapun. Total stok master: {{ number_format((float)$barang->stok, 0) }} {{ $barang->unit?->symbol }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- TAB 2: STOCK MOVEMENTS LEDGER --}}
            <div class="tab-pane fade p-3" id="tab-stock-movement" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Jenis Transaksi</th>
                                <th>Serial Number</th>
                                <th>Perubahan Qty</th>
                                <th>Saldo Sebelum</th>
                                <th>Saldo Sesudah</th>
                                <th>Lokasi</th>
                                <th>Petugas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stockMovements as $mv)
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
                                        @if ($mv->inventoryItem)
                                            <a href="{{ route('inventory-item.show', $mv->inventory_item_id) }}" class="fw-bold text-primary">
                                                <code>{{ $mv->inventoryItem->serial_number }}</code>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ (float)$mv->quantity > 0 ? 'text-success' : ((float)$mv->quantity < 0 ? 'text-danger' : 'text-muted') }}">
                                            {{ (float)$mv->quantity > 0 ? '+' : '' }}{{ number_format((float)$mv->quantity, $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>{{ number_format((float)$mv->quantity_before, $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}</td>
                                    <td>{{ number_format((float)$mv->quantity_after, $barang->unit?->is_decimal ? 2 : 0, ',', '.') }}</td>
                                    <td>{{ $mv->ruangan?->nama_ruangan ?? '-' }}</td>
                                    <td>{{ $mv->user?->name ?? 'Sistem' }}</td>
                                    <td><small class="text-muted">{{ $mv->keterangan ?? '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Belum ada riwayat mutasi stok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $stockMovements->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH SERIAL BARU --}}
    @if ($barang->has_serial_number)
        <div class="modal fade" id="modalTambahSerial" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('inventory-item.store') }}" method="POST" class="modal-content">
                    @csrf
                    <input type="hidden" name="barang_id" value="{{ $barang->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-barcode me-1"></i> Tambah Unit Serial: {{ $barang->nama }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nomor Seri (Serial Number) <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" class="form-control" placeholder="Contoh: SN-004 / FO-2026-99" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kuantitas Unit (Qty) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="any" min="0.01" name="initial_quantity" class="form-control" value="1" required />
                                <span class="input-group-text">{{ $barang->unit?->symbol }}</span>
                            </div>
                            <small class="text-muted">Untuk kabel/material, masukkan panjang atau berat aktual.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi / Ruangan</label>
                            <select name="ruangan_id" class="form-select">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Unit</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL QUICK TRANSFER --}}
        <div class="modal fade" id="modalQuickTransfer" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formQuickTransfer" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-transfer me-1"></i> Pindah Lokasi Unit Serial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Serial Number: <strong id="transferSerialName" class="text-primary"></strong></p>
                        <p class="mb-3 small text-muted">Lokasi Saat Ini: <span id="transferCurrentRoom"></span></p>

                        <div class="mb-3">
                            <label class="form-label">Pilih Ruangan Tujuan <span class="text-danger">*</span></label>
                            <select name="to_ruangan_id" class="form-select" required>
                                <option value="">-- Pilih Ruangan Tujuan --</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan Perpindahan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Dipindahkan untuk pemakaian lab" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Pindahkan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL QUICK ADJUSTMENT --}}
        <div class="modal fade" id="modalQuickAdjust" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formQuickAdjust" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-slider-alt me-1"></i> Penyesuaian Stok (Stock Opname)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Serial Number: <strong id="adjustSerialName" class="text-primary"></strong></p>
                        
                        <div class="mb-3">
                            <label class="form-label">Quantity Hasil Pengecekan Fisik <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="any" min="0" name="new_quantity" id="adjustNewQty" class="form-control" required />
                                <span class="input-group-text">{{ $barang->unit?->symbol }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Unit Baru</label>
                            <select name="new_status" id="adjustNewStatus" class="form-select">
                                <option value="available">Tersedia (Available)</option>
                                <option value="damaged">Rusak (Damaged)</option>
                                <option value="lost">Hilang (Lost)</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="depleted">Habis Terpakai (Depleted)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penyesuaian <span class="text-danger">*</span></label>
                            <input type="text" name="alasan" class="form-control" placeholder="Contoh: Hasil stock opname bulanan / kabel terpotong" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="bx bx-save me-1"></i> Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function confirmDeleteItem(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Jika unit memiliki riwayat transaksi, statusnya akan diubah menjadi habis/nonaktif.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-item-' + id).submit();
                }
            });
        }

        function openTransferModal(id, serial, currentRoom) {
            document.getElementById('transferSerialName').innerText = serial;
            document.getElementById('transferCurrentRoom').innerText = currentRoom;
            document.getElementById('formQuickTransfer').action = "{{ url('admin/inventory-item') }}/" + id + "/transfer";
            new bootstrap.Modal(document.getElementById('modalQuickTransfer')).show();
        }

        function openAdjustModal(id, serial, currentQty, currentStatus) {
            document.getElementById('adjustSerialName').innerText = serial;
            document.getElementById('adjustNewQty').value = currentQty;
            document.getElementById('adjustNewStatus').value = currentStatus;
            document.getElementById('formQuickAdjust').action = "{{ url('admin/inventory-item') }}/" + id + "/adjust";
            new bootstrap.Modal(document.getElementById('modalQuickAdjust')).show();
        }
    </script>
@endsection
