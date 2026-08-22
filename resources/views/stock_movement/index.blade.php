@extends('layouts.admin')
@section('page-title', 'Buku Mutasi & Audit Trail Stok')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            {{-- Header Action --}}
            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>Buku Mutasi Stok & Audit Trail Terpusat</h5>
                    <small class="text-muted">Mencatat seluruh rekam jejak mutasi IN, OUT, BORROW, RETURN, TRANSFER, dan ADJUSTMENT.</small>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('stock-adjustment.index') }}" class="btn btn-warning">
                        <i class="bx bx-slider-alt me-1"></i> Form Koreksi Stok (Opname)
                    </a>

                    <form action="{{ route('stock-movement.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="type" value="{{ request('type') }}">
                        <input type="hidden" name="barang_id" value="{{ request('barang_id') }}">
                        <input type="hidden" name="ruangan_id" value="{{ request('ruangan_id') }}">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                        <button type="submit" name="export" value="pdf" class="btn btn-outline-danger">
                            <i class="bx bxs-file-pdf me-1"></i> Ekspor PDF
                        </button>
                        <button type="submit" name="export" value="excel" class="btn btn-outline-success">
                            <i class="bx bx-spreadsheet me-1"></i> Ekspor Excel
                        </button>
                    </form>
                </div>
            </div>

            {{-- Filter Multi-Parameter --}}
            <form action="{{ route('stock-movement.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Kata kunci, serial, keterangan..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Jenis Mutasi</label>
                        <select name="type" class="form-select">
                            <option value="">Semua Mutasi</option>
                            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>IN (Barang Masuk)</option>
                            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>OUT (Barang Keluar)</option>
                            <option value="borrow" {{ request('type') === 'borrow' ? 'selected' : '' }}>BORROW (Peminjaman)</option>
                            <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>RETURN (Pengembalian)</option>
                            <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>TRANSFER (Pindah Ruangan)</option>
                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>ADJUSTMENT (Opname)</option>
                            <option value="initial" {{ request('type') === 'initial' ? 'selected' : '' }}>INITIAL (Saldo Awal)</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Master Barang</label>
                        <select name="barang_id" class="form-select">
                            <option value="">Semua Barang</option>
                            @foreach ($barangs as $b)
                                <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Lokasi / Ruangan</label>
                        <select name="ruangan_id" class="form-select">
                            <option value="">Semua Ruangan</option>
                            @foreach ($ruangans as $r)
                                <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <div class="w-50">
                            <label class="form-label small fw-semibold">Dari Tgl</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="w-50">
                            <label class="form-label small fw-semibold">Sampai Tgl</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-search me-1"></i> Terapkan Filter
                        </button>
                        @if (request()->hasAny(['search', 'type', 'barang_id', 'ruangan_id', 'start_date', 'end_date']))
                            <a href="{{ route('stock-movement.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="bx bx-refresh me-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap mb-2">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Waktu Transaksi</th>
                        <th>Tipe Mutasi</th>
                        <th>Master Barang</th>
                        <th>Unit Serial</th>
                        <th>Perubahan Qty</th>
                        <th>Saldo Sebelum</th>
                        <th>Saldo Sesudah</th>
                        <th>Lokasi / Ruangan</th>
                        <th>Petugas</th>
                        <th>Referensi & Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $m)
                        <tr>
                            <td>{{ $loop->iteration + ($movements->firstItem() - 1) }}</td>
                            <td>
                                <div class="fw-semibold">{{ $m->tanggal?->translatedFormat('d M Y') }}</div>
                                <small class="text-muted">{{ $m->tanggal?->format('H:i:s') }} WIB</small>
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
                                        'in' => 'IN (Masuk)',
                                        'out' => 'OUT (Keluar)',
                                        'borrow' => 'BORROW (Pinjam)',
                                        'return' => 'RETURN (Kembali)',
                                        'transfer' => 'TRANSFER (Pindah)',
                                        'adjustment' => 'ADJUSTMENT (Opname)',
                                        'initial' => 'INITIAL (Awal)',
                                    ];
                                @endphp
                                <span class="badge {{ $typeBadges[$m->type] ?? 'bg-label-secondary' }}">
                                    {{ $typeLabels[$m->type] ?? strtoupper($m->type) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('barang.show', $m->barang_id) }}" class="fw-semibold text-dark">
                                    {{ $m->barang?->nama }}
                                </a>
                                <div><small class="text-muted">{{ $m->barang?->merek }}</small></div>
                            </td>
                            <td>
                                @if ($m->inventoryItem)
                                    <a href="{{ route('inventory-item.show', $m->inventory_item_id) }}">
                                        <code class="fw-bold text-primary">{{ $m->inventoryItem->serial_number }}</code>
                                    </a>
                                @else
                                    <span class="badge bg-label-secondary">Non-Serial</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold fs-6 {{ (float)$m->quantity > 0 ? 'text-success' : ((float)$m->quantity < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ (float)$m->quantity > 0 ? '+' : '' }}{{ number_format((float)$m->quantity, $m->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                </span>
                                <span class="badge bg-label-dark ms-1">{{ $m->barang?->unit?->symbol ?? 'pcs' }}</span>
                            </td>
                            <td>{{ number_format((float)$m->quantity_before, $m->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</td>
                            <td><strong class="text-dark">{{ number_format((float)$m->quantity_after, $m->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</strong></td>
                            <td>
                                @if ($m->ruangan)
                                    <i class="bx bx-map-pin text-primary me-1"></i>{{ $m->ruangan->nama_ruangan }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $m->user?->name ?? 'Sistem' }}</td>
                            <td>
                                @if ($m->reference_type)
                                    <span class="badge bg-label-secondary small me-1">{{ $m->reference_type }} #{{ $m->reference_id }}</span>
                                @endif
                                <small class="text-muted d-block">{{ $m->keterangan ?? '-' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                Tidak ada data rekam jejak mutasi stok yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-3">
            {{ $movements->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
