@extends('layouts.admin')
@section('page-title', 'Data Peminjaman')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="p-3">
    {{-- Tombol Aksi --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            <i class="bx bx-folder-plus"></i> Tambah Peminjaman
        </a>

        <form action="{{ route('peminjaman.index') }}" method="GET">
            <div class="d-flex flex-wrap gap-2">
            <button type="submit" name="export" value="pdf" class="btn btn-danger">
                <i class="bx bxs-file-pdf"></i> Ekspor PDF
            </button>

            <button type="submit" name="export" value="excel" class="btn btn-success">
                <i class="bx bx-spreadsheet"></i> Ekspor Excel
            </button>
            </div>
    </div>

    {{-- Form Filter & Pencarian --}}
    <div class="card p-3 shadow-sm">
        <div class="row g-3">
            {{-- Keyword Pencarian --}}
            <div class="col-md-6">
                <label for="search" class="form-label">Cari Peminjaman</label>
                <input type="text" name="search" class="form-control"
                    placeholder="Nama peminjam, barang, atau ruangan..."
                    value="{{ request('search') }}">
            </div>

            {{-- Filter Tanggal Pinjam --}}
            <div class="col-md-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ request('end_date') }}">
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-search"></i> Cari
            </button>

            @if ((request()->has('search') && request()->search != '') || request()->has('start_date') || request()->has('end_date'))
                <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                    <i class="bx bx-refresh"></i> Reset
                </a>
            @endif
        </div>
</div>
</form>
</div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Peminjaman</th>
                        <th>Nama Barang</th>
                        <th>Kode Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Nama Peminjam</th>
                        <th>Ruangan</th>
                        <th>Status</th>
                        <th>Tenggat</th>
                        <th>Kreator</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($peminjaman as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($peminjaman->firstItem() - 1) }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->barang->nama . ' - ' . $item->barang->merek }}</td>
                            <td>{{ $item->barang->kode_barang }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ $item->nama_peminjam }}</td>
                            <td>{{ $item->ruangan->nama_ruangan }}</td>
                            <td
                                class="{{ $item->status == 'Sedang Dipinjam' ? 'text-danger' : ($item->status == 'Sudah Dikembalikan' ? 'text-success' : '') }}">
                                {{ $item->status }}
                            </td>
                            <td>
                                @if ($item->tenggat === 'Terlambat')
                                    <span class="text-danger">{{ $item->tenggat }}</span>
                                @else
                                    <span class="text-success">{{ $item->tenggat }}</span>
                                @endif
                            </td>
                            <td>{{ $item->user->name }}</td>

                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <!-- Tombol Detail -->
                                         
                                        <a class="dropdown-item" href="{{ route('peminjaman.show', $item->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </a>

                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('peminjaman.edit', $item->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('peminjaman.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- Tombol Hapus (trigger SweetAlert) -->
                                        <a href="#" class="dropdown-item text-danger"
                                            onclick="confirmDelete({{ $item->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="m-4">
            {{ $peminjaman->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

@endsection
