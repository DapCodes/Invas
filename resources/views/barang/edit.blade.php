@extends('layouts.admin')
@section('page-title', 'Ubah Master Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Ubah Data Master Barang</h5>
                    <small class="text-muted">Kode Barang: <strong class="text-primary">{{ $barang->kode_barang }}</strong></small>
                </div>
                <a href="{{ route('barang.show', $barang->id) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama">Nama Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-collection"></i></span>
                                <input name="nama" type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                    value="{{ old('nama', $barang->nama) }}" required />
                            </div>
                            @error('nama')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="merek">Merek / Brand <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                                <input name="merek" type="text" id="merek" class="form-control @error('merek') is-invalid @enderror"
                                    value="{{ old('merek', $barang->merek) }}" required />
                            </div>
                            @error('merek')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="satuan_id">Satuan Standar <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-ruler"></i></span>
                                <select name="satuan_id" id="satuan_id" class="form-select @error('satuan_id') is-invalid @enderror" required>
                                    @foreach ($units as $u)
                                        <option value="{{ $u->id }}" {{ old('satuan_id', $barang->satuan_id) == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->symbol }}) - {{ $u->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('satuan_id')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor_id">Vendor / Supplier</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                                    <option value="">-- Tidak Ada Vendor --</option>
                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id }}" {{ old('vendor_id', $barang->vendor_id) == $v->id ? 'selected' : '' }}>
                                            {{ $v->name }} {{ $v->code ? '('.$v->code.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vendor_id')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="deskripsi">Deskripsi</label>
                        <div class="col-sm-10">
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tipe Inventaris</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control bg-light" readonly
                                value="{{ $barang->has_serial_number ? 'SERIALIZED (Memiliki unit/serial number)' : 'NON-SERIALIZED (Stok Berdasarkan Kuantitas)' }}" />
                            <small class="text-muted">Tipe inventaris telah ditentukan saat pembuatan master barang.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="foto">Foto Barang</label>
                        <div class="col-sm-10">
                            @if ($barang->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('image/barang/' . $barang->foto) }}" alt="{{ $barang->nama }}"
                                        class="rounded shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*" />
                            <small class="text-muted">Upload gambar baru jika ingin mengganti foto saat ini.</small>
                            @error('foto')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="is_active">Status Barang</label>
                        <div class="col-sm-10">
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $barang->is_active) ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !old('is_active', $barang->is_active) ? 'selected' : '' }}>Nonaktif / Diarsipkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('barang.show', $barang->id) }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
