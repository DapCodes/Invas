@extends('layouts.admin')
@section('page-title', 'Data Vendor')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">

            {{-- Tombol Tambah & Ekspor --}}
            <div class="mb-3 d-flex flex-wrap gap-2">
                <a href="{{ route('vendor.create') }}" class="btn btn-primary">
                    <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i> Tambah Data Vendor
                </a>

                <form action="{{ route('vendor.index') }}" method="GET">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" name="export" value="pdf" class="btn btn-danger">
                            <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i> Ekspor PDF
                        </button>

                        <button type="submit" name="export" value="excel" class="btn btn-success">
                            <i class="bx bx-spreadsheet" style="position: relative; bottom: 2px;"></i> Ekspor Excel
                        </button>
                    </div>
                </form>
            </div>

            <form action="{{ route('vendor.index') }}" method="GET" class="card p-3 shadow-sm mb-3">
                <div class="row g-3 align-items-end">

                    {{-- Pencarian --}}
                    <div class="col-md-8">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Kode, nama vendor, email, telepon..."
                            value="{{ request('search') }}">
                    </div>

                    {{-- Tombol --}}
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        @if (request()->has('search'))
                            <a href="{{ route('vendor.index') }}" class="btn btn-secondary">
                                <i class="bx bx-refresh"></i> Reset
                            </a>
                        @endif
                    </div>

                </div>
            </form>

        </div>

        <div class="table-responsive text-nowrap mb-2">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Vendor</th>
                        <th>Nama Vendor</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Jumlah Barang</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($vendors as $data)
                        <tr>
                            <td>{{ $loop->iteration + ($vendors->firstItem() - 1) }}</td>
                            <td>
                                <span class="fw-semibold">{{ $data->code ?? '-' }}</span>
                            </td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->phone ?? '-' }}</td>
                            <td>{{ $data->email ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-primary fs-7">{{ $data->barangs_count }} Barang</span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item" href="{{ route('vendor.show', $data->id) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('vendor.edit', $data->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete -->
                                        <form id="form-delete-{{ $data->id }}"
                                            action="{{ route('vendor.destroy', $data->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- Tombol Hapus (trigger SweetAlert) -->
                                        <a href="#" class="dropdown-item text-danger"
                                            onclick="confirmDelete({{ $data->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bx bx-info-circle fs-3 d-block mb-2"></i>
                                Belum ada vendor. Tambahkan vendor pertama Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-4">
            {{ $vendors->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
