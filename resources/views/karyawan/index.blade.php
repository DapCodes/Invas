@extends('layouts.admin')
@section('page-title', 'Data Petugas')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="p-3">

    {{-- Tombol Tambah & Ekspor --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        {{-- Tambah Karyawan --}}
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
            <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i> Tambah Data
        </a>

        {{-- Ekspor PDF & Excel --}}
        <form action="{{ route('karyawan.index') }}" method="GET" >
            <div>
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">

            <button type="submit" name="export" value="pdf" class="btn btn-danger">
                <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i> Ekspor PDF
            </button>

            <button type="submit" name="export" value="excel" class="btn btn-success">
                <i class="bx bx-spreadsheet" style="position: relative; bottom: 2px;"></i> Ekspor Excel
            </button>
</div>
    </div>

    {{-- Form Pencarian --}}
    <div class="card p-3 shadow-sm mb-3">
        <div class="row g-3 align-items-end">

            {{-- Input Pencarian --}}
            <div class="col-md-6 col-lg-4">
                <label for="search" class="form-label">Pencarian</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Nama, jurusan..."
                    value="{{ request('search') }}">
            </div>

            {{-- Tombol Aksi --}}
            <div class="col-md-6 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i> Cari
                </button>

                @if (request()->has('search') || request()->has('start_date') || request()->has('end_date'))
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($users as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($users->firstItem() - 1) }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('l, d F Y') }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('karyawan.edit', $item->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Ubah
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('karyawan.destroy', $item->id) }}" method="POST"
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
            {{ $users->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

@endsection
