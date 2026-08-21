@extends('layouts.admin')
@section('page-title', 'Data Ruangan')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="p-3">
    {{-- Tombol Ekspor --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        <form action="{{ route('brg-ruangan.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <div class="d-flex flex-wrap gap-2">
            <button type="submit" name="export" value="pdf" class="btn btn-danger">
                <i class="bx bxs-file-pdf"></i> Ekspor PDF
            </button>

            <button type="submit" name="export" value="excel" class="btn btn-success">
                <i class="bx bx-spreadsheet"></i> Ekspor Excel
            </button>
</div>
    </div>

    {{-- Form Filter dan Pencarian --}}
    <div class="card p-3 shadow-sm">
        <div class="row g-3">
            {{-- Filter Ruangan --}}
            <div class="col-md-4">
                <label for="byClassSelect" class="form-label">Filter Berdasarkan Ruangan</label>
                <select name="byClass" id="byClassSelect" class="form-select text-center">
                    <option value="">Semua Ruangan</option>
                    @foreach ($ruangan as $item)
                        <option value="{{ $item->id }}" {{ isset($byClass) && $byClass == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom Pencarian --}}
            <div class="col-md-4">
                <label for="search" class="form-label">Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="Nama barang..."
                    value="{{ request('search') }}">
            </div>

            {{-- Tombol Cari & Reset --}}
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="bx bx-search"></i> Cari
                </button>

                @if (
                    (request()->has('search') && request()->search != '') ||
                        request()->has('start_date') ||
                        request()->has('end_date') ||
                        request()->has('byClass'))
                    <a href="{{ route('brg-ruangan.index') }}" class="btn btn-secondary">
                        <i class="bx bx-refresh"></i> Reset
                    </a>
                @endif
            </div>
        </div>
                </div>
                </form>
</div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Ruangan</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($barangRuangan as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($barangRuangan->firstItem() - 1) }}</td>
                            <td>{{ $item->ruangan->nama_ruangan }}</td>
                            <td>{{ $item->barang->nama }} - {{ $item->barang->merek }}</td>
                            <td>{{ $item->stok }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item"
                                            href="{{ route('brg-ruangan.show', $item->id, $item->id_barang) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
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
            {{ $barangRuangan->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

@endsection
