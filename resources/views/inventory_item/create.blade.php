@extends('layouts.admin')
@section('page-title', 'Tambah Unit Serial Number')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah Unit Serial Number Baru</h5>
                <a href="{{ $barang ? route('barang.show', $barang->id) : route('inventory-item.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory-item.store') }}" method="POST">
                    @csrf

                    {{-- PILIH BARANG --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="barang_id">Master Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            @if ($barang)
                                <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                <input type="text" class="form-control bg-light" readonly
                                    value="{{ $barang->nama }} ({{ $barang->merek }}) - Satuan: {{ $barang->unit?->symbol ?? 'unit' }}" />
                            @else
                                <select name="barang_id" id="barang_id" class="form-select @error('barang_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Barang Berserial --</option>
                                    @foreach ($barangs as $b)
                                        <option value="{{ $b->id }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->nama }} ({{ $b->merek }}) - Satuan: {{ $b->unit?->symbol }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('barang_id')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    {{-- SERIAL NUMBER --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="serial_number">Nomor Seri (Serial Number) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                                    placeholder="Contoh: SN-IP17-001 / FO-2026-001" value="{{ old('serial_number') }}" required />
                            </div>
                            @error('serial_number')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- KUANTITAS --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="initial_quantity">Kuantitas Unit (Qty) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                <input type="number" step="any" min="0.01" name="initial_quantity" id="initial_quantity"
                                    class="form-control @error('initial_quantity') is-invalid @enderror"
                                    placeholder="1.00" value="{{ old('initial_quantity', 1) }}" required />
                            </div>
                            <small class="text-muted">Untuk unit biasa (iPhone, Laptop), masukkan 1. Untuk material berkelanjutan (Kabel), masukkan panjang/berat awal (misal 200 meter).</small>
                            @error('initial_quantity')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- RUANGAN --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="ruangan_id">Lokasi / Ruangan</label>
                        <div class="col-sm-10">
                            <select name="ruangan_id" id="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror">
                                <option value="">-- Pilih Ruangan / Lokasi --</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruangan_id')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- TANGGAL MASUK --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="tanggal_masuk">Tanggal Masuk</label>
                        <div class="col-sm-10">
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control"
                                value="{{ old('tanggal_masuk', date('Y-m-d')) }}" />
                        </div>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="keterangan">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2"
                                placeholder="Catatan spesifikasi / kondisi awal...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Simpan Unit Serial
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
