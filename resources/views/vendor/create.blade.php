@extends('layouts.admin')
@section('page-title', 'Data Vendor / Tambah')

@section('content')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah data vendor</h5>
                <a href="{{ route('vendor.index') }}">
                    <button class="btn btn-outline-secondary">
                        Kembali
                    </button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('vendor.store') }}" method="POST">
                    @csrf
                    
                    {{-- Nama Vendor --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor-name">Nama Vendor <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="vendor-name"
                                    placeholder="PT ABC Jaya" value="{{ old('name') }}" required />
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Kode Vendor --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor-code">Kode Vendor</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                <input name="code" type="text" id="vendor-code" class="form-control @error('code') is-invalid @enderror"
                                    placeholder="Opsional (Otomatis jika dikosongkan)" value="{{ old('code') }}" />
                            </div>
                            @error('code')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nomor Telepon --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor-phone">Telepon</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input name="phone" type="text" id="vendor-phone" class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="08123456789" value="{{ old('phone') }}" />
                            </div>
                            @error('phone')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor-email">Email</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input name="email" type="email" id="vendor-email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="vendor@example.com" value="{{ old('email') }}" />
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor-address">Alamat</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                <textarea name="address" id="vendor-address" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Jl. Merdeka No. 123, Jakarta" rows="2">{{ old('address') }}</textarea>
                            </div>
                            @error('address')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor-description">Deskripsi</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-detail"></i></span>
                                <textarea name="description" id="vendor-description" class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Keterangan vendor..." rows="3">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
