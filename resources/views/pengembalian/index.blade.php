@extends('layouts.admin')
@section('page-title', 'Data Pengembalian Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            {{-- Header Action --}}
            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <a href="{{ route('pengembalian.create') }}" class="btn btn-success">
                    <i class="bx bx-undo me-1"></i> Tambah Pengembalian
                </a>

                <form action="{{ route('pengembalian.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="kondisi" value="{{ request('kondisi') }}">

                    <button type="submit" name="export" value="pdf" class="btn btn-outline-danger">
                        <i class="bx bxs-file-pdf me-1"></i> Ekspor PDF
                    </button>
                    <button type="submit" name="export" value="excel" class="btn btn-outline-success">
                        <i class="bx bx-spreadsheet me-1"></i> Ekspor Excel
                    </button>
                </form>
            </div>

            {{-- Filter & Pencarian --}}
            <form action="{{ route('pengembalian.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Kode, peminjam, barang, serial..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="kondisi" class="form-label small fw-semibold">Kondisi Barang</label>
                        <select name="kondisi" id="kondisi" class="form-select">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" {{ request('kondisi') === 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak" {{ request('kondisi') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Sebagian Rusak" {{ request('kondisi') === 'Sebagian Rusak' ? 'selected' : '' }}>Sebagian Rusak</option>
                            <option value="Hilang" {{ request('kondisi') === 'Hilang' ? 'selected' : '' }}>Hilang</option>
                            <option value="Tidak Lengkap" {{ request('kondisi') === 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap</option>
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
                        @if (request()->hasAny(['search', 'start_date', 'end_date', 'kondisi']))
                            <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-secondary" title="Reset">
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
                        <th>Kode Kembali</th>
                        <th>Peminjam</th>
                        <th>Barang Master</th>
                        <th>Unit Serial</th>
                        <th>Qty Kembali</th>
                        <th>Kondisi</th>
                        <th>Ruangan Masuk</th>
                        <th>Tgl Kembali</th>
                        <th>Petugas</th>
                        <th class="text-center" style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengembalian as $data)
                        <tr>
                            <td>{{ $loop->iteration + ($pengembalian->firstItem() - 1) }}</td>
                            <td>
                                <a href="{{ route('pengembalian.show', $data->id) }}" class="fw-bold text-success">
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
                                <span class="fw-bold fs-6 text-success">
                                    +{{ number_format((float)$data->jumlah, $data->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                </span>
                                <span class="badge bg-label-dark ms-1">{{ $data->barang?->unit?->symbol ?? 'pcs' }}</span>
                            </td>
                            <td>
                                @php
                                    $condBadges = [
                                        'Baik' => 'bg-label-success',
                                        'Rusak' => 'bg-label-danger',
                                        'Sebagian Rusak' => 'bg-label-warning',
                                        'Hilang' => 'bg-label-dark',
                                        'Tidak Lengkap' => 'bg-label-secondary',
                                    ];
                                @endphp
                                <span class="badge {{ $condBadges[$data->kondisi] ?? 'bg-label-secondary' }}">
                                    {{ $data->kondisi }}
                                </span>
                            </td>
                            <td>{{ $data->ruangan?->nama_ruangan ?? '-' }}</td>
                            <td>{{ $data->tanggal_kembali?->translatedFormat('d M Y') }}</td>
                            <td>{{ $data->user?->name ?? 'Sistem' }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('pengembalian.show', $data->id) }}">
                                            <i class="bx bx-show me-1 text-info"></i> Detail
                                        </a>
                                        <form id="form-del-ret-{{ $data->id }}" action="{{ route('pengembalian.destroy', $data->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="dropdown-item text-danger" onclick="confirmDeleteReturn({{ $data->id }})">
                                            <i class="bx bx-trash me-1"></i> Batalkan
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                Tidak ada data pengembalian yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-3">
            {{ $pengembalian->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <script>
        function confirmDeleteReturn(id) {
            Swal.fire({
                title: 'Batalkan Pengembalian?',
                text: "Status peminjaman dan stok akan dikembalikan ke kondisi sebelum pengembalian!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-del-ret-' + id).submit();
                }
            });
        }
    </script>
@endsection
