@extends('layouts.admin')
@section('page-title', 'Data Master Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            {{-- Tombol Tambah & Ekspor --}}
            <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('barang.create') }}" class="btn btn-primary">
                        <i class="bx bx-folder-plus me-1"></i> Tambah Master Barang
                    </a>
                </div>

                <form action="{{ route('barang.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <button type="submit" name="export" value="pdf" class="btn btn-outline-danger">
                        <i class="bx bxs-file-pdf me-1"></i> Ekspor PDF
                    </button>
                    <button type="submit" name="export" value="excel" class="btn btn-outline-success">
                        <i class="bx bx-spreadsheet me-1"></i> Ekspor Excel
                    </button>
                </form>
            </div>

            {{-- Form Pencarian & Filter --}}
            <form action="{{ route('barang.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Nama, kode, merek, serial number..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="type" class="form-label small fw-semibold">Tipe Barang</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="serialized" {{ request('type') === 'serialized' ? 'selected' : '' }}>Serialized (Punya SN)</option>
                            <option value="non_serialized" {{ request('type') === 'non_serialized' ? 'selected' : '' }}>Non-Serialized (Quantity Saja)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="vendor_id" class="form-label small fw-semibold">Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-select">
                            <option value="">Semua Vendor</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'type', 'vendor_id', 'satuan_id']))
                            <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                        <th style="width: 50px;">#</th>
                        <th>Kode</th>
                        <th>Foto</th>
                        <th>Nama Barang & Merk</th>
                        <th>Tipe / Serial</th>
                        <th>Total Stok</th>
                        <th>Vendor</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($barang as $data)
                        <tr>
                            <td>{{ $loop->iteration + ($barang->firstItem() - 1) }}</td>
                            <td>
                                <a href="{{ route('barang.show', $data->id) }}" class="fw-bold text-primary">
                                    {{ $data->kode_barang }}
                                </a>
                            </td>
                            <td>
                                @if ($data->foto)
                                    <a href="{{ asset('image/barang/' . $data->foto) }}" target="_blank">
                                        <img src="{{ asset('image/barang/' . $data->foto) }}" alt="{{ $data->nama }}"
                                            class="rounded shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                                    </a>
                                @else
                                    <div class="avatar avatar-sm bg-label-secondary d-flex align-items-center justify-content-center rounded">
                                        <i class="bx bx-package"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $data->nama }}</div>
                                <small class="text-muted"><i class="bx bx-buildings me-1"></i>{{ $data->merek }}</small>
                            </td>
                            <td>
                                @if ($data->has_serial_number)
                                    <span class="badge bg-label-info mb-1">
                                        <i class="bx bx-barcode me-1"></i> Serialized
                                    </span>
                                    <div>
                                        <small class="text-muted">{{ $data->inventory_items_count }} Unit Terdaftar</small>
                                    </div>
                                @else
                                    <span class="badge bg-label-secondary">
                                        <i class="bx bx-cube me-1"></i> Non-Serial
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold fs-6 {{ (float)$data->stok > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format((float)$data->stok, $data->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                </span>
                                <span class="badge bg-label-dark ms-1">{{ $data->unit?->symbol ?? 'unit' }}</span>
                            </td>
                            <td>
                                @if ($data->vendor)
                                    <span class="fw-medium">{{ $data->vendor->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($data->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('barang.show', $data->id) }}">
                                            <i class="bx bx-show me-1 text-info"></i> Detail & Stok
                                        </a>
                                        <a class="dropdown-item" href="{{ route('barang.edit', $data->id) }}">
                                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit
                                        </a>
                                        <form id="form-delete-{{ $data->id }}" action="{{ route('barang.destroy', $data->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="dropdown-item text-danger" onclick="confirmDelete({{ $data->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus / Nonaktifkan
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                                Tidak ada data master barang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-3">
            {{ $barang->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Barang yang sudah ada riwayat transaksi akan dinonaktifkan secara aman!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + id).submit();
                }
            });
        }
    </script>
@endsection
