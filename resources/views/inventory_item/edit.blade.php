@extends('layouts.admin')
@section('page-title', 'Ubah Unit Serial: ' . $item->serial_number)

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Ubah Data Unit Serial Number</h5>
                    <small class="text-muted">Master: <strong>{{ $item->barang?->nama }}</strong></small>
                </div>
                <a href="{{ route('inventory-item.show', $item->id) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory-item.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="serial_number">Nomor Seri <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                                    value="{{ old('serial_number', $item->serial_number) }}" required />
                            </div>
                            @error('serial_number')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kuantitas Saat Ini</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control bg-light" readonly
                                value="{{ number_format((float)$item->current_quantity, $item->barang?->unit?->is_decimal ? 2 : 0) }} {{ $item->barang?->unit?->symbol }}" />
                            <small class="text-muted">Untuk mengubah kuantitas stok fisik, gunakan fitur Penyesuaian / Stock Opname di halaman detail.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="status">Status Unit <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="available" {{ old('status', $item->status) === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                                <option value="borrowed" {{ old('status', $item->status) === 'borrowed' ? 'selected' : '' }}>Sedang Dipinjam (Borrowed)</option>
                                <option value="in_use" {{ old('status', $item->status) === 'in_use' ? 'selected' : '' }}>Sedang Digunakan (In Use)</option>
                                <option value="out" {{ old('status', $item->status) === 'out' ? 'selected' : '' }}>Keluar (Out)</option>
                                <option value="damaged" {{ old('status', $item->status) === 'damaged' ? 'selected' : '' }}>Rusak (Damaged)</option>
                                <option value="lost" {{ old('status', $item->status) === 'lost' ? 'selected' : '' }}>Hilang (Lost)</option>
                                <option value="maintenance" {{ old('status', $item->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="depleted" {{ old('status', $item->status) === 'depleted' ? 'selected' : '' }}>Habis (Depleted)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="ruangan_id">Lokasi / Ruangan</label>
                        <div class="col-sm-10">
                            <select name="ruangan_id" id="ruangan_id" class="form-select">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id', $item->ruangan_id) == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Perubahan ruangan akan secara otomatis dicatat pada riwayat perpindahan lokasi.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="keterangan">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2">{{ old('keterangan', $item->keterangan) }}</textarea>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('inventory-item.show', $item->id) }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
