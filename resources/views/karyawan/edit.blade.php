@extends('layouts.admin')
@section('page-title', 'Data Petugas / Ubah')

@section('content')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Ubah data karyawan</h5>
                <a href="{{ route('karyawan.index') }}">
                    <button class="btn btn-outline-secondary">
                        Kembali
                    </button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Nama</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukan nama karyawan" autocomplete="name" value="{{ $karyawan->name }}"
                                    required />
                            </div>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                <i class="bx bx-error-circle"></i>
                                <p>{{ $message }}</p>
                            </div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Email</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-envelope"></i></span>
                                <input type="email" value="{{ $karyawan->email }}" class="form-control" id="email"
                                    name="email" placeholder="Masukan Email" utocomplete="email" required />
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Kata Sandi Baru</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                <input id="password" type="password" class="form-control" name="password"
                                    placeholder="Masukan Kata Sandi Baru (Opsional)">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="password_confirmation">Konfirmasi Sandi</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text cursor-pointer"><i class="bx bx-lock"></i></span>
                                <input id="password_confirmation" type="password" class="form-control"
                                    name="password_confirmation" placeholder="Konfirmasi Kata Sandi Baru (Opsional)">
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Status Petugas</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <select name="status_user" id="status_user"
                                    class="form-control @error('status_user') is-invalid @enderror">
                                    <option value="Umum"
                                        {{ old('status_user', $karyawan->status_user ?? '') == 'Umum' ? 'selected' : '' }}>
                                        Petugas Umum</option>
                                    <option value="RPL"
                                        {{ old('status_user', $karyawan->status_user ?? '') == 'RPL' ? 'selected' : '' }}>
                                        Petugas RPL</option>
                                    <option value="TBSM"
                                        {{ old('status_user', $karyawan->status_user ?? '') == 'TBSM' ? 'selected' : '' }}>
                                        Petugas TBSM</option>
                                    <option value="TKRO"
                                        {{ old('status_user', $karyawan->status_user ?? '') == 'TKRO' ? 'selected' : '' }}>
                                        Petugas TKRO</option>
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
