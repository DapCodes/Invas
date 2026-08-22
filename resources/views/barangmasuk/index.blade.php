@extends('layouts.admin')
@section('page-title', 'Data Penerimaan Barang Masuk')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            {{-- Header Action --}}
            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <a href="{{ route('brg-masuk.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Barang Masuk
                </a>

                <form action="{{ route('brg-masuk.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
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

            {{-- Filter & Pencarian --}}
            <form action="{{ route('brg-masuk.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Kode transaksi, nama barang, serial, ruangan..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="start_date" class="form-label small fw-semibold">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label small fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'start_date', 'end_date']))
                            <a href="{{ route('brg-masuk.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="bx bx-refresh"></i>
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
                        <th>Kode Transaksi</th>
                        <th>Barang Master</th>
                        <th>Unit Serial</th>
                        <th>Jumlah Masuk</th>
                        <th>Lokasi Ruangan</th>
                        <th>Tanggal Masuk</th>
                        <th>Keterangan</th>
                        <th>Petugas</th>
                        <th class="text-center" style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangMasuk as $data)
                        <tr>
                            <td>{{ $loop->iteration + ($barangMasuk->firstItem() - 1) }}</td>
                            <td>
                                <a href="{{ route('brg-masuk.show', $data->id) }}" class="fw-bold text-primary">
                                    {{ $data->kode_barang }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('barang.show', $data->id_barang) }}" class="fw-semibold text-dark">
                                    {{ $data->barang?->nama ?? '-' }}
                                </a>
                                <div><small class="text-muted">{{ $data->barang?->merek }}</small></div>
                            </td>
                            <td>
                                @if ($data->inventoryItem)
                                    <a href="{{ route('inventory-item.show', $data->inventoryItem->id) }}">
                                        <code class="fw-bold text-info">{{ $data->inventoryItem->serial_number }}</code>
                                    </a>
                                @else
                                    <span class="badge bg-label-secondary">Non-Serial</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold fs-6 text-success">
                                    +{{ number_format((float)$data->jumlah, $data->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                </span>
                                <span class="badge bg-label-dark ms-1">{{ $data->barang?->unit?->symbol ?? 'pcs' }}</span>
                            </td>
                            <td>
                                @if ($data->ruangan)
                                    <i class="bx bx-map-pin text-primary me-1"></i>{{ $data->ruangan->nama_ruangan }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $data->tanggal_masuk?->translatedFormat('d M Y') }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($data->keterangan, 35) }}</small>
                            </td>
                            <td>{{ $data->user?->name ?? 'Sistem' }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('brg-masuk.show', $data->id) }}">
                                            <i class="bx bx-show me-1 text-info"></i> Detail
                                        </a>
                                        <form id="form-delete-bm-{{ $data->id }}" action="{{ route('brg-masuk.destroy', $data->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="dropdown-item text-danger" onclick="confirmDeleteBm({{ $data->id }})">
                                            <i class="bx bx-undo me-1"></i> Batalkan / Void
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                Tidak ada data transaksi barang masuk ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-3">
            {{ $barangMasuk->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <script>
        function confirmDeleteBm(id) {
            Swal.fire({
                title: 'Batalkan Transaksi Barang Masuk?',
                text: "Stok akan dikembalikan dan dicatat dalam riwayat pembatalan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan Transaksi',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-bm-' + id).submit();
                }
            });
        }
    </script>
@endsection
