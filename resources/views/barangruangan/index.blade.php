@extends('layouts.admin')
@section('page-title', 'Stok & Penempatan Barang per Ruangan')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            {{-- Header Action --}}
            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTransferStok">
                    <i class="bx bx-transfer me-1"></i> Transfer Stok Antar Ruangan
                </button>

                <form action="{{ route('brg-ruangan.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="byClass" value="{{ request('byClass') }}">

                    <button type="submit" name="export" value="pdf" class="btn btn-outline-danger">
                        <i class="bx bxs-file-pdf me-1"></i> Ekspor PDF
                    </button>
                    <button type="submit" name="export" value="excel" class="btn btn-outline-success">
                        <i class="bx bx-spreadsheet me-1"></i> Ekspor Excel
                    </button>
                </form>
            </div>

            {{-- Filter & Pencarian --}}
            <form action="{{ route('brg-ruangan.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Nama barang, merek, atau ruangan..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="byClass" class="form-label small fw-semibold">Filter Berdasarkan Ruangan</label>
                        <select name="byClass" id="byClass" class="form-select">
                            <option value="">Semua Ruangan</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}" {{ request('byClass') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'byClass']))
                            <a href="{{ route('brg-ruangan.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="bx bx-refresh"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL 1: ALOKASI STOK NON-SERIAL --}}
        <div class="p-3 pt-0">
            <h6 class="fw-bold text-primary mb-2"><i class="bx bx-building me-1"></i> Daftar Stok Barang per Ruangan (Non-Serial)</h6>
            <div class="table-responsive text-nowrap mb-4">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Nama Ruangan</th>
                            <th>Master Barang</th>
                            <th>Merek</th>
                            <th>Stok Tersedia</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangRuangan as $data)
                            <tr>
                                <td>{{ $loop->iteration + ($barangRuangan->firstItem() - 1) }}</td>
                                <td>
                                    <i class="bx bx-map-pin text-primary me-1"></i>
                                    <strong>{{ $data->ruangan?->nama_ruangan ?? '-' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('barang.show', $data->barang_id) }}" class="fw-bold text-dark">
                                        {{ $data->barang?->nama ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $data->barang?->merek ?? '-' }}</td>
                                <td>
                                    <span class="fw-bold fs-6 text-success">
                                        {{ number_format((float)$data->stok, $data->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                    </span>
                                    <span class="badge bg-label-dark ms-1">{{ $data->barang?->unit?->symbol ?? 'pcs' }}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-outline-primary"
                                        onclick="openQuickTransferNonSerial({{ $data->barang_id }}, '{{ $data->barang?->nama }}', {{ $data->ruangan_id }}, '{{ $data->ruangan?->nama_ruangan }}', {{ $data->stok }})">
                                        <i class="bx bx-transfer me-1"></i> Transfer
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Tidak ada data stok barang ruangan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                {{ $barangRuangan->links('vendor.pagination.bootstrap-5') }}
            </div>

            {{-- TABEL 2: UNIT SERIAL YANG DITEMPATKAN DI RUANGAN INI (JIKA FILTER RUANGAN AKTIF) --}}
            @if ($byClass && count($serializedInRoom) > 0)
                <div class="border-top pt-3">
                    <h6 class="fw-bold text-info mb-2"><i class="bx bx-barcode me-1"></i> Unit Serial yang Berada di Ruangan Ini ({{ count($serializedInRoom) }} Unit)</h6>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nomor Seri</th>
                                    <th>Barang</th>
                                    <th>Kuantitas</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serializedInRoom as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('inventory-item.show', $item->id) }}" class="fw-bold text-primary">
                                                <code>{{ $item->serial_number }}</code>
                                            </a>
                                        </td>
                                        <td>{{ $item->barang?->nama }} ({{ $item->barang?->merek }})</td>
                                        <td>{{ number_format((float)$item->current_quantity, 2) }} {{ $item->barang?->unit?->symbol }}</td>
                                        <td>
                                            <span class="badge bg-label-success">{{ $item->status }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('inventory-item.show', $item->id) }}" class="btn btn-xs btn-outline-info">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL TRANSFER STOK ANTAR RUANGAN --}}
    <div class="modal fade" id="modalTransferStok" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('brg-ruangan.transfer') }}" method="POST" class="modal-content" id="formTransferModal">
                @csrf
                <input type="hidden" name="type" id="transferType" value="non_serialized">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-transfer me-1"></i> Transfer Stok Antar Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Barang Non-Serial --}}
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang Non-Serial <span class="text-danger">*</span></label>
                        <select name="barang_id" id="transferBarangId" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangsNonSerial as $b)
                                <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->merek }}) - Satuan: {{ $b->unit?->symbol }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dari Ruangan --}}
                    <div class="mb-3">
                        <label class="form-label">Dari Ruangan Asal <span class="text-danger">*</span></label>
                        <select name="from_ruangan_id" id="transferFromRoom" class="form-select" required>
                            <option value="">-- Pilih Ruangan Asal --</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ke Ruangan --}}
                    <div class="mb-3">
                        <label class="form-label">Ke Ruangan Tujuan <span class="text-danger">*</span></label>
                        <select name="to_ruangan_id" id="transferToRoom" class="form-select" required>
                            <option value="">-- Pilih Ruangan Tujuan --</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jumlah --}}
                    <div class="mb-3">
                        <label class="form-label">Jumlah Yang Dipindahkan <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0.01" name="quantity" id="transferQty" class="form-control" placeholder="1.00" required />
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Alasan Transfer</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Pemindahan kursi untuk rapat di Ruang Meeting" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Proses Transfer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openQuickTransferNonSerial(barangId, barangNama, fromRoomId, fromRoomNama, maxStock) {
            document.getElementById('transferBarangId').value = barangId;
            document.getElementById('transferFromRoom').value = fromRoomId;
            document.getElementById('transferQty').value = maxStock;
            new bootstrap.Modal(document.getElementById('modalTransferStok')).show();
        }
    </script>
@endsection
