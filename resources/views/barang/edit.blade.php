@extends('layouts.admin')
@section('page-title', 'Data Barang / Ubah')

@section('content')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah data barang</h5>
                <a href="{{ route('barang.index') }}">
                    <button class="btn btn-outline-secondary">
                        Kembali
                    </button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="text" hidden name="kode_barang" value="{{ $barang->kode_barang }}">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Nama Barang</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                        class="bx bx-collection"></i></span>
                                <input value="{{ $barang->nama }}" name="nama" type="text" class="form-control"
                                    id="basic-icon-default-fullname" placeholder="Handphone" aria-label="Handphone"
                                    aria-describedby="basic-icon-default-fullname2" />
                            </div>
                            @error('nama')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Merek</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-buildings"></i></span>
                                <input value="{{ $barang->merek }}" name="merek" type="text"
                                    id="basic-icon-default-company" class="form-control" placeholder="Samsung"
                                    aria-label="Samsung" aria-describedby="basic-icon-default-company2" />
                            </div>
                            @error('merek')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="vendor_id">Vendor</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                                    <option value="">Tidak ada vendor</option>
                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id }}" {{ old('vendor_id', $barang->vendor_id) == $v->id ? 'selected' : '' }}>
                                            {{ $v->name }} {{ $v->code ? '('.$v->code.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vendor_id')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="serial_number">Nomor Seri</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                <input name="serial_number" type="text" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                                    placeholder="Masukkan nomor seri" value="{{ old('serial_number', $barang->serial_number) }}" />
                            </div>
                            @error('serial_number')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">Foto</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-image-add"></i></span>
                                <input value="{{ $barang->foto }}" type="file" name="foto"
                                    id="basic-icon-default-phone" class="form-control phone-mask" placeholder="658 799 8941"
                                    aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" />
                            </div>
                            @error('foto')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    @php
                        $status = Auth::user()->status_user;
                    @endphp
                    @if ($status == 'RPL')
                        <input type="text" name="status_barang" id="status_barang" value="RPL" hidden>
                    @endif
                    @if ($status == 'TBSM')
                        <input type="text" name="status_barang" id="status_barang" value="TBSM" hidden>
                    @endif
                    @if ($status == 'TKRO')
                        <input type="text" name="status_barang" id="status_barang" value="TKRO" hidden>
                    @endif
                    @if ($status == 'admin')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Status Petugas</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <select name="status_barang" id="status_barang"
                                        class="form-control @error('status_barang') is-invalid @enderror">
                                        <option value="Umum"
                                            {{ old('status_barang', $barang->status_barang ?? '') == 'Umum' ? 'selected' : '' }}>
                                            Barang Umum</option>
                                        <option value="RPL"
                                            {{ old('status_barang', $barang->status_barang ?? '') == 'RPL' ? 'selected' : '' }}>
                                            Barang RPL</option>
                                        <option value="TBSM"
                                            {{ old('status_barang', $barang->status_barang ?? '') == 'TBSM' ? 'selected' : '' }}>
                                            Barang TBSM</option>
                                        <option value="TKRO"
                                            {{ old('status_barang', $barang->status_barang ?? '') == 'TKRO' ? 'selected' : '' }}>
                                            Barang TKRO</option>
                                    </select>
                                </div>
                                @error('status_user')
                                    <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                        <i class="bx bx-error-circle"></i>
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Ubah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
