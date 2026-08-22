@extends('layouts.admin')
@section('page-title', 'Detail Ruangan: ' . $ruangan->nama_ruangan)

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xl bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="bx bx-building fs-1"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">{{ $ruangan->nama_ruangan }}</h4>
                        <div class="text-muted small">
                            <span><i class="bx bx-info-circle me-1"></i> {{ $ruangan->deskripsi ?? 'Tidak ada deskripsi ruangan' }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('brg-ruangan.index', ['byClass' => $ruangan->id]) }}" class="btn btn-primary">
                        <i class="bx bx-list-ul me-1"></i> Kelola Stok Ruangan Ini
                    </a>
                    <a href="{{ route('ruangan.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-non-serial">
                    <i class="bx bx-cube me-1"></i> Stok Barang Non-Serial ({{ count($nonSerialStocks) }})
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-serial-units">
                    <i class="bx bx-barcode me-1"></i> Unit Serial Fisik ({{ count($serialUnits) }})
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-location-timeline">
                    <i class="bx bx-transfer me-1"></i> Riwayat Perpindahan Lokasi (Transfer)
                </button>
            </li>
        </ul>

        <div class="tab-content p-0 shadow-sm border-top-0">
            {{-- TAB 1: NON SERIAL --}}
            <div class="tab-pane fade show active p-3" id="tab-non-serial" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Master Barang</th>
                                <th>Merek</th>
                                <th>Stok di Ruangan Ini</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($nonSerialStocks as $stock)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('barang.show', $stock->barang_id) }}" class="fw-bold text-dark">
                                            {{ $stock->barang?->nama }}
                                        </a>
                                    </td>
                                    <td>{{ $stock->barang?->merek }}</td>
                                    <td>
                                        <strong class="fs-6 text-success">{{ number_format((float)$stock->stok, $stock->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</strong>
                                    </td>
                                    <td><span class="badge bg-label-secondary">{{ $stock->barang?->unit?->symbol }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada alokasi barang non-serial di ruangan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: SERIAL UNITS --}}
            <div class="tab-pane fade p-3" id="tab-serial-units" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nomor Seri</th>
                                <th>Master Barang</th>
                                <th>Kuantitas</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serialUnits as $unit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('inventory-item.show', $unit->id) }}" class="fw-bold text-primary">
                                            <code>{{ $unit->serial_number }}</code>
                                        </a>
                                    </td>
                                    <td>{{ $unit->barang?->nama }} ({{ $unit->barang?->merek }})</td>
                                    <td>
                                        <strong class="text-success">{{ number_format((float)$unit->current_quantity, $unit->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}</strong> {{ $unit->barang?->unit?->symbol }}
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">{{ $unit->status }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('inventory-item.show', $unit->id) }}" class="btn btn-xs btn-outline-info">
                                            <i class="bx bx-show me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada unit serial fisik di ruangan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: LOCATION HISTORY TIMELINE --}}
            <div class="tab-pane fade p-3" id="tab-location-timeline" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Unit Serial & Barang</th>
                                <th>Dari Ruangan</th>
                                <th>Ke Ruangan</th>
                                <th>Petugas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTransfers as $tf)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $tf->tanggal?->translatedFormat('d M Y') }}</div>
                                        <small class="text-muted">{{ $tf->tanggal?->format('H:i') }} WIB</small>
                                    </td>
                                    <td>
                                        <code>{{ $tf->inventoryItem?->serial_number }}</code>
                                        <div><small class="text-muted">{{ $tf->inventoryItem?->barang?->nama }}</small></div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tf->from_ruangan_id == $ruangan->id ? 'bg-label-warning' : 'bg-label-secondary' }}">
                                            {{ $tf->fromRuangan?->nama_ruangan ?? 'Belum Ada' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tf->to_ruangan_id == $ruangan->id ? 'bg-label-success' : 'bg-label-primary' }}">
                                            {{ $tf->toRuangan?->nama_ruangan ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $tf->user?->name ?? 'Sistem' }}</td>
                                    <td><small class="text-muted">{{ $tf->keterangan ?? '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat mutasi perpindahan lokasi untuk ruangan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
