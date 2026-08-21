@extends('layouts.admin')
@section('page-title', 'Data Peminjaman / Ubah')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Ubah Status Peminjaman</h5>
                <a href="{{ route('peminjaman.index') }}">
                    <button class="btn btn-outline-secondary">Kembali</button>
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- ID Barang -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-company">Pilih Barang</label>
                        <div class="col-sm-10">
                            <div class="dropdown">
                                <button style="text-align: left;" class="w-100 btn btn-outline-secondary dropdown-toggle"
                                    type="button" id="dropdownBarang" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i style="position: relative; right: 8px; bottom: 2px;" class="bx bx-box"></i>
                                    Pilih Barang
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownBarang" style="width: 100%;">
                                    @foreach ($barang as $item)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start" href="#"
                                                onclick="pilihBarang('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->merek }}')">
                                                <img src="{{ asset('image/barang/' . $item->foto) }}"
                                                    alt="{{ $item->nama }}" width="50" height="50"
                                                    class="me-3 rounded">
                                                <div class="flex-grow-1">
                                                    <div><strong>{{ $item->nama }}</strong> - {{ $item->merek }}</div>
                                                    <small class="text-muted">Stok: {{ $item->stok }}</small>
                                                </div>
                                            </a>
                                            <hr class="my-1">
                                        </li>
                                    @endforeach
                                </ul>
                                @error('id_barang')
                                    <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                        <i class="bx bx-error-circle"></i>
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <input type="hidden" name="id_barang" id="id_barang">

                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">Ruangan</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <select name="ruangan_id" id="ruangan_id" class="form-control">
                                    @foreach ($ruangan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == $peminjaman->ruangan_id ? 'selected' : '' }}>
                                            {{ $item->nama_ruangan }}
                                        </option>
                                    @endforeach
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

                    <!-- Jumlah -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-hash"></i></span>
                                <input type="number" class="form-control" name="jumlah"
                                    value="{{ old('jumlah', $peminjaman->jumlah) }}">
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tanggal Pinjam -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Pinjam</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-calendar"></i></span>
                                <input type="date" class="form-control" name="tanggal_pinjam"
                                    value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam) }}">
                            </div>
                            @error('tanggal_pinjam')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tanggal Kembali -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Kembali</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i
                                        class="bx bx-calendar"></i></span>
                                <input type="date" class="form-control" name="tanggal_kembali"
                                    value="{{ old('tanggal_kembali', $peminjaman->tanggal_kembali) }}">
                            </div>
                            @error('tanggal_kembali')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nama Peminjam -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Peminjam</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text"><i <i
                                        class="bx bx-user"></i></span>
                                <input type="text" class="form-control" name="nama_peminjam"
                                    value="{{ old('nama_peminjam', $peminjaman->nama_peminjam) }}">

                            </div>
                            @error('nama_peminjam')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status (Bisa diubah) -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select name="status" class="form-control">
                                <option value="Sedang Dipinjam"
                                    {{ $peminjaman->status == 'Sedang Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam
                                </option>
                                <option value="Sudah Dikembalikan"
                                    {{ $peminjaman->status == 'Sudah Dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block mt-1 d-flex gap-1" style="margin-left: 15px;">
                                    <i class="bx bx-error-circle"></i>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tombol Submit -->
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


@isset($peminjaman)
    @php
        $selectedBarangId = old('id_barang', $peminjaman->id_barang);
        $selectedBarang = $barang->firstWhere('id', $selectedBarangId);
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectedId = '{{ $selectedBarangId }}';
            const dropdownBtn = document.getElementById('dropdownBarang');
            const inputIdBarang = document.getElementById('id_barang');

            if (selectedId) {
                inputIdBarang.value = selectedId;
                dropdownBtn.innerHTML = `
                    <i style="position: relative; right: 8px; bottom: 2px;" class="bx bx-box"></i>
                    {{ $selectedBarang ? $selectedBarang->nama . ' - ' . $selectedBarang->merek : 'Pilih Barang' }}`;
            }
        });

        function pilihBarang(id, nama, merek) {
            document.getElementById('id_barang').value = id;
            document.getElementById('dropdownBarang').innerHTML =
                `<i style="position: relative; right: 8px; bottom: 2px;" class="bx bx-box"></i> ${nama} - ${merek}`;
        }
    </script>
@endisset
