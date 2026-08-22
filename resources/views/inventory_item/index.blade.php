@extends('layouts.admin')
@section('page-title', 'Daftar Unit Serial Number')

@section('content')
    @include('sweetalert::alert')

    <div class="card mb-5">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold"><i class="bx bx-barcode me-2"></i>Daftar Unit Serial Number Fisik</h5>
                <a href="{{ route('inventory-item.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Unit Serial
                </a>
            </div>

            {{-- Filter & Search Form --}}
            <form action="{{ route('inventory-item.index') }}" method="GET" class="card p-3 shadow-sm mb-3 bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Serial number, nama barang..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Master Barang</label>
                        <select name="barang_id" class="form-select">
                            <option value="">Semua Barang</option>
                            @foreach ($barangs as $b)
                                <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }} ({{ $b->merek }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Status Unit</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>Digunakan</option>
                            <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Keluar</option>
                            <option value="damaged" {{ request('status') === 'damaged' ? 'selected' : '' }}>Rusak</option>
                            <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Hilang</option>
                            <option value="depleted" {{ request('status') === 'depleted' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Ruangan / Lokasi</label>
                        <select name="ruangan_id" class="form-select">
                            <option value="">Semua Ruangan</option>
                            @foreach ($ruangans as $r)
                                <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'status', 'ruangan_id', 'barang_id']))
                            <a href="{{ route('inventory-item.index') }}" class="btn btn-outline-secondary" title="Reset">
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
                        <th>Nomor Seri</th>
                        <th>Barang Master</th>
                        <th>Sisa Qty</th>
                        <th>Status</th>
                        <th>Lokasi / Ruangan</th>
                        <th>Tgl Masuk</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($items->firstItem() - 1) }}</td>
                            <td>
                                <a href="{{ route('inventory-item.show', $item->id) }}" class="fw-bold text-primary">
                                    <code>{{ $item->serial_number }}</code>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('barang.show', $item->barang_id) }}" class="fw-semibold text-dark">
                                    {{ $item->barang?->nama }}
                                </a>
                                <div><small class="text-muted">{{ $item->barang?->merek }}</small></div>
                            </td>
                            <td>
                                <span class="fw-bold fs-6 {{ (float)$item->current_quantity > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ number_format((float)$item->current_quantity, $item->barang?->unit?->is_decimal ? 2 : 0, ',', '.') }}
                                </span>
                                <span class="badge bg-label-secondary ms-1">{{ $item->barang?->unit?->symbol ?? 'unit' }}</span>
                            </td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'available' => 'bg-label-success',
                                        'borrowed' => 'bg-label-warning',
                                        'in_use' => 'bg-label-info',
                                        'out' => 'bg-label-secondary',
                                        'damaged' => 'bg-label-danger',
                                        'lost' => 'bg-label-danger',
                                        'depleted' => 'bg-label-dark',
                                    ];
                                    $statusLabels = [
                                        'available' => 'Tersedia',
                                        'borrowed' => 'Sedang Dipinjam',
                                        'in_use' => 'Sedang Digunakan',
                                        'out' => 'Keluar',
                                        'damaged' => 'Rusak',
                                        'lost' => 'Hilang',
                                        'depleted' => 'Habis',
                                    ];
                                @endphp
                                <span class="badge {{ $statusBadges[$item->status] ?? 'bg-label-secondary' }}">
                                    {{ $statusLabels[$item->status] ?? $item->status }}
                                </span>
                            </td>
                            <td>
                                @if ($item->ruangan)
                                    <i class="bx bx-map-pin text-primary me-1"></i>{{ $item->ruangan->nama_ruangan }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('inventory-item.show', $item->id) }}">
                                            <i class="bx bx-show me-1 text-info"></i> Detail & Riwayat
                                        </a>
                                        <a class="dropdown-item" href="{{ route('inventory-item.edit', $item->id) }}">
                                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit
                                        </a>
                                        <form id="form-del-{{ $item->id }}" action="{{ route('inventory-item.destroy', $item->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="dropdown-item text-danger" onclick="confirmDel({{ $item->id }})">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Tidak ada data unit serial yang sesuai dengan kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-3">
            {{ $items->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <script>
        function confirmDel(id) {
            Swal.fire({
                title: 'Hapus Unit Serial?',
                text: "Jika unit memiliki riwayat transaksi, status akan diubah menjadi nonaktif/habis.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-del-' + id).submit();
                }
            });
        }
    </script>
@endsection
