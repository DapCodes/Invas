@extends('layouts.admin')
@section('page-title', 'Data Barang')

@section('content')
    @include('sweetalert::alert')


    <div class="card mb-5">
       <div class="p-3">

        {{-- Tombol Tambah & Ekspor --}}
        <div class="mb-3 d-flex flex-wrap gap-2">
            <a href="{{ route('barang.create') }}" class="btn btn-primary">
                <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i> Tambah Data Barang
            </a>

            <form action="{{ route('barang.index') }}" method="GET" >
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" name="export" value="pdf" class="btn btn-danger">
                    <i class="bx bxs-file-pdf" style="position: relative; bottom: 2px;"></i> Ekspor PDF
                </button>

                <button type="submit" name="export" value="excel" class="btn btn-success">
                    <i class="bx bx-spreadsheet" style="position: relative; bottom: 2px;"></i> Ekspor Excel
                </button>
            </div>
        </div>

        <div action="{{ route('barang.index') }}" method="GET" class="card p-3 shadow-sm mb-3">
        <div class="row g-3 align-items-end">

            {{-- Pencarian --}}
            <div class="col-md-4">
                <label for="search" class="form-label">Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="Nama, kode, merek..."
                    value="{{ request('search') }}">
            </div>

            {{-- Filter Status --}}
            @if (Auth::user()->status_user == 'admin')
                <div class="col-md-4">
                    <label for="status_barang" class="form-label">Status Barang</label>
                    <select name="status_barang" id="status_barang" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $option)
                            <option value="{{ $option }}" {{ request('status_barang') == $option ? 'selected' : '' }}>
                                {{ strtoupper($option) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Tombol --}}
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i> Cari
                </button>
                @if (request()->has('search') || request()->has('status_barang'))
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="bx bx-refresh"></i> Reset
                    </a>
                @endif
            </div>

        </div>
</div>
</form>


        

    </div>


        <div class="table-responsive text-nowrap mb-2">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Vendor</th>
                        <th>Nomor Seri</th>
                        <th>Foto</th>
                        <th>Stok</th>
                        @php
                            $status = Auth::user()->status_user;
                        @endphp
                        @if ($status == 'admin')
                            <th>Status</th>
                        @endif
                        <th>Kreator</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($barang as $data)
                        <tr>
                            <td>{{ $loop->iteration + ($barang->firstItem() - 1) }}</td>
                            <td>{{ $data->kode_barang }}</td>
                            <td>{{ $data->nama }}</td>
                            <td>{{ $data->merek }}</td>
                            <td>
                                @if ($data->vendor)
                                    <a href="{{ route('vendor.show', $data->vendor->id) }}" class="fw-semibold text-primary">
                                        {{ $data->vendor->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($data->serial_number)
                                    <code>{{ $data->serial_number }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="../image/barang/{{ $data->foto }}" target="_blank">
                                    <img style="width: 50px; border-radius: 5px; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.1);"
                                        src="{{ asset('/image/barang/' . $data->foto) }}" alt="">
                                </a>
                            </td>
                            <td>{{ $data->stok }}</td>
                            @if ($status == 'admin')
                                <td>{{ $data->status_barang }}</td>
                            @endif
                            <td>{{ $data->user->name }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <!-- Tombol Show -->
                                        <a class="dropdown-item" href="{{ route('barang.show', $data->id) }}">
                                            <i class="bx bx-show me-1"></i> Lihat
                                        </a>
                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="{{ route('barang.edit', $data->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $data->id }}"
                                            action="{{ route('barang.destroy', $data->id) }}" method="POST"
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
                    @endforeach
                </tbody>
            </table>

        </div>
        <div class="m-4">
            {{ $barang->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

@endsection
