@extends('layouts.admin')
@section('page-title', 'Data Pengembalian')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="p-3">
    {{-- Tombol Ekspor --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        <form action="{{ route('pengembalian.index') }}" method="GET">
            <div class="d-flex flex-wrap gap-2">
            <button type="submit" name="export" value="pdf" class="btn btn-danger">
                <i class="bx bxs-file-pdf"></i> Ekspor PDF
            </button>

            <button type="submit" name="export" value="excel" class="btn btn-success">
                <i class="bx bx-spreadsheet"></i> Ekspor Excel
            </button>
</div>
    </div>

    {{-- Form Pencarian dan Filter Tanggal --}}
    <div class="card p-3 shadow-sm">
        <div class="row g-3">
            {{-- Kolom Pencarian --}}
            <div class="col-md-6">
                <label for="search" class="form-label">Cari Data</label>
                <input type="text" name="search" class="form-control"
                    placeholder="Nama peminjam, barang..." value="{{ request('search') }}">
            </div>

            {{-- Filter Tanggal --}}
            <div class="col-md-3">
                <label for="start_date" class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                <i class="bx bx-search"></i> Cari
            </button>

            @if ((request()->has('search') && request()->search != '') || request()->has('start_date') || request()->has('end_date'))
                <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">
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
                        <th>Kode pengembalian</th>
                        <th>Nama Barang</th>
                        <th>Kode Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Nama Peminjam</th>
                        <th>Ruangan</th>
                        <th>Status</th>
                        <th>Kreator</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($pengembalian as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($pengembalian->firstItem() - 1) }}</td>
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
                            <td>{{ $item->user->name }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <!-- Tombol Detail -->
                                        <a class="dropdown-item" href="{{ route('pengembalian.show', $item->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('pengembalian.destroy', $item->id) }}" method="POST"
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
            {{ $pengembalian->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

@endsection
