@extends('layouts.admin')
@section('page-title', 'Data Ruangan')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="p-3">

    {{-- Tombol Tambah & Ekspor --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        {{-- Tombol Tambah Ruangan (Trigger Modal) --}}
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRuangan">
            <i class="bx bx-folder-plus" style="position: relative; bottom: 2px;"></i> Tambah Ruangan
        </button>

        {{-- Form Ekspor PDF & Excel --}}
        <form action="{{ route('ruangan.index') }}" method="GET" >
            <div class="d-flex flex-wrap gap-2">
            <input type="hidden" name="search" value="{{ request('search') }}">
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

            {{-- Pencarian --}}
            <div class="col-md-6 col-lg-4">
                <label for="search" class="form-label">Pencarian</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Nama ruangan..."
                    value="{{ request('search') }}">
            </div>

            {{-- Tambahkan filter lainnya di sini jika diperlukan --}}
            
            <div class="col-md-6 col-lg-4">
    <label for="status" class="form-label">Status Ruangan</label>
    <select name="status" id="status" class="form-select">
        <option value="">Semua Jurusan</option>
        <option value="RPL" {{ request('status') == 'RPL' ? 'selected' : '' }}>RPL</option>
        <option value="TBSM" {{ request('status') == 'TBSM' ? 'selected' : '' }}>TBSM</option>
        <option value="TKRO" {{ request('status') == 'TKRO' ? 'selected' : '' }}>TKRO</option>
        <option value="Umum" {{ request('status') == 'Umum' ? 'selected' : '' }}>Umum</option>
    </select>
</div>

        

            {{-- Tombol Aksi --}}
            <div class="col-md-6 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i> Cari
                </button>
                @if (request()->filled('search'))
                    <a href="{{ route('ruangan.index') }}" class="btn btn-secondary">
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
                        <th>deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($ruangan as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($ruangan->firstItem() - 1) }}</td>
                            <td>{{ $item->nama_ruangan }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">

                                        <!-- Tombol Edit -->
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#modalEditRuangan-{{ $item->id }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>


                                        <!-- Form Delete (Disembunyikan) -->
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('ruangan.destroy', $item->id) }}" method="POST"
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
            {{ $ruangan->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>




    <!-- Modal Tambah Ruangan -->
    <div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-labelledby="modalTambahRuanganLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('ruangan.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahRuanganLabel">Tambah Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Jurusan</label>
                            <select name="deskripsi" id="" class="form-control">
                                <option value="Umum">Umum</option>
                                <option value="TBSM">TBSM</option>
                                <option value="RPL">RPL</option>
                                <option value="TKRO">TKRO</option>

                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @foreach ($ruangan as $item)
        <!-- Modal Edit Ruangan -->
        <div class="modal fade" id="modalEditRuangan-{{ $item->id }}" tabindex="-1"
            aria-labelledby="modalEditRuanganLabel-{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('ruangan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditRuanganLabel-{{ $item->id }}">Edit Ruangan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                                <input type="text" class="form-control" name="nama_ruangan"
                                    value="{{ $item->nama_ruangan }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Jurusan</label>
                                <select name="deskripsi" class="form-control">
                                    <option value="Umum" {{ $item->deskripsi == 'Umum' ? 'selected' : '' }}>Umum
                                    </option>
                                    <option value="TBSM" {{ $item->deskripsi == 'TBSM' ? 'selected' : '' }}>TBSM
                                    </option>
                                    <option value="RPL" {{ $item->deskripsi == 'RPL' ? 'selected' : '' }}>RPL</option>
                                    <option value="TKRO" {{ $item->deskripsi == 'TKRO' ? 'selected' : '' }}>TKRO
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach


@endsection
