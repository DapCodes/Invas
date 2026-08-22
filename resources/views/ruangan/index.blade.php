@extends('layouts.admin')
@section('page-title', 'Data Ruangan')

@section('content')
    @include('sweetalert::alert')
    <div class="card">
        <div class="p-3">

            {{-- Tombol Tambah & Ekspor --}}
            <div class="mb-3 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRuangan">
                    <i class="bx bx-folder-plus me-1"></i> Tambah Ruangan
                </button>

                <form action="{{ route('ruangan.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <button type="submit" name="export" value="pdf" class="btn btn-outline-danger">
                        <i class="bx bxs-file-pdf me-1"></i> Ekspor PDF
                    </button>
                    <button type="submit" name="export" value="excel" class="btn btn-outline-success">
                        <i class="bx bx-spreadsheet me-1"></i> Ekspor Excel
                    </button>
                </form>
            </div>

            {{-- Form Pencarian --}}
            <div class="card p-3 shadow-sm mb-3 bg-light">
                <form action="{{ route('ruangan.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-4">
                        <label for="search" class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Nama ruangan, deskripsi..."
                            value="{{ request('search') }}">
                    </div>

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
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Nama Ruangan</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Item / Unit</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ruangan as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($ruangan->firstItem() - 1) }}</td>
                            <td>
                                <a href="{{ route('ruangan.show', $item->id) }}" class="fw-bold text-primary">
                                    <i class="bx bx-map-pin me-1"></i>{{ $item->nama_ruangan }}
                                </a>
                            </td>
                            <td>{{ $item->deskripsi ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-primary me-1">{{ $item->barang_ruangan_count }} Jenis Stok</span>
                                <span class="badge bg-label-info">{{ $item->inventory_items_count }} Serial</span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('ruangan.show', $item->id) }}">
                                            <i class="bx bx-show me-1 text-info"></i> Detail Isi Ruangan
                                        </a>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEditRuangan-{{ $item->id }}">
                                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit
                                        </a>
                                        <form id="form-delete-{{ $item->id }}" action="{{ route('ruangan.destroy', $item->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="dropdown-item text-danger" onclick="confirmDeleteRuangan({{ $item->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data ruangan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="m-4">
            {{ $ruangan->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <!-- Modal Tambah Ruangan -->
    <div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('ruangan.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Ruangan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_ruangan" class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" placeholder="Contoh: Ruang Server Lt. 2" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" name="deskripsi" id="deskripsi" placeholder="Deskripsi peruntukan ruangan...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach ($ruangan as $item)
        <!-- Modal Edit Ruangan -->
        <div class="modal fade" id="modalEditRuangan-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('ruangan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Ruangan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_ruangan" value="{{ $item->nama_ruangan }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" name="deskripsi" value="{{ $item->deskripsi }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        function confirmDeleteRuangan(id) {
            Swal.fire({
                title: 'Hapus Ruangan?',
                text: "Ruangan hanya dapat dihapus jika sudah tidak ada stok atau unit serial di dalamnya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + id).submit();
                }
            });
        }
    </script>
@endsection
