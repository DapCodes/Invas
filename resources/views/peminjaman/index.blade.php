@extends('layouts.admin')
@section('page-title', 'Data Peminjaman Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            {{-- Header Action --}}
            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Peminjaman
                </a>

                <form action="{{ route('peminjaman.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">

                    <button type="submit" name="export" value="pdf" class="btn btn-outline-danger">
                        <i class="bx bxs-file-pdf me-1"></i> Ekspor PDF
                    </button>
                    <button type="submit" name="export" value="excel" class="btn btn-outline-success">
                        <i class="bx bx-spreadsheet me-1"></i> Ekspor Excel
                    </button>
                </form>
            </div>

            {{-- Filter & Pencarian --}}
            <form action="{{ route('peminjaman.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Kode, peminjam, barang, serial..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label small fw-semibold">Status Peminjaman</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Sedang Dipinjam" {{ request('status', 'Sedang Dipinjam') === 'Sedang Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                            <option value="Sudah Dikembalikan" {{ request('status') === 'Sudah Dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                            <option value="Semua" {{ request('status') === 'Semua' ? 'selected' : '' }}>Semua Status</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label small fw-semibold">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label small fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'start_date', 'end_date', 'status']))
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary" title="Reset">
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
                        <th>Kode Pinjam</th>
                        <th>Peminjam</th>
                        <th>Barang Master</th>
                        <th>Unit Serial</th>
                        <th>Qty Pinjam</th>
                        <th>Sisa Belum Kembali</th>
                        <th>Tgl Pinjam / Batas</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjaman as $data)
                        <tr>
                            <td>{{ $loop->iteration + ($peminjaman->firstItem() - 1) }}</td>
                            <td>
                                <a href="{{ route('peminjaman.show', $data->id) }}" class="fw-bold text-primary">
                                    {{ $data->kode_barang }}
                                </a>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $data->nama_peminjam }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('barang.show', $data->id_barang) }}" class="fw-semibold text-dark">
                                    {{ $data->barang?->nama }}
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
                                <span class="fw-bold">
                                    {{ number_format((float)$data->jumlah, $data->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                </span>
                                <span class="badge bg-label-dark ms-1">{{ $data->barang?->unit?->symbol ?? 'pcs' }}</span>
                            </td>
                            <td>
                                @if ($data->outstanding_qty > 0)
                                    <span class="badge bg-label-warning fw-bold">
                                        {{ number_format((float)$data->outstanding_qty, $data->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }} {{ $data->barang?->unit?->symbol }}
                                    </span>
                                @else
                                    <span class="badge bg-label-success">Lengkap (0)</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $data->tanggal_pinjam?->translatedFormat('d M Y') }}</div>
                                <small class="text-muted">s/d {{ $data->tanggal_kembali?->translatedFormat('d M Y') }}</small>
                            </td>
                            <td>
                                @if ($data->status === 'Sedang Dipinjam')
                                    @if ($data->tenggat === 'Terlambat')
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-warning">Dipinjam</span>
                                    @endif
                                @else
                                    <span class="badge bg-success">Dikembalikan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('peminjaman.show', $data->id) }}">
                                            <i class="bx bx-show me-1 text-info"></i> Detail Transaksi
                                        </a>
                                        @if ($data->status === 'Sedang Dipinjam' && $data->outstanding_qty > 0)
                                            <a class="dropdown-item text-success fw-bold" href="{{ route('pengembalian.create', ['peminjaman_id' => $data->id]) }}">
                                                <i class="bx bx-undo me-1"></i> Proses Pengembalian
                                            </a>
                                        @endif
                                        <form id="form-del-loan-{{ $data->id }}" action="{{ route('peminjaman.destroy', $data->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="dropdown-item text-danger" onclick="confirmDeleteLoan({{ $data->id }})">
                                            <i class="bx bx-trash me-1"></i> Batalkan Pinjam
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                Tidak ada data peminjaman yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-3">
            {{ $peminjaman->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <script>
        function confirmDeleteLoan(id) {
            Swal.fire({
                title: 'Batalkan Peminjaman?',
                text: "Status barang akan dikembalikan menjadi tersedia di inventaris!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan Peminjaman',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-del-loan-' + id).submit();
                }
            });
        }
    </script>
@endsection
