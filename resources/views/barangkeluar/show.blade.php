@extends('layouts.admin')
@section('page-title', 'Data Barang Keluar / Lihat')

@section('content')
    @include('sweetalert::alert')

    <div class="row g-4 mb-5">
        <!-- Gambar dan info singkat produk -->
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <img class="card-img-top mt-5" src="{{ asset('/image/barang/' . $barangKeluar->barang->foto) }}"
                    alt="Card image cap" />
                <div class="card-body">

                </div>
            </div>
        </div>

        <!-- Detail dan aksi pembelian -->
        <div class="col-md-12 col-lg-8">

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">{{ $barangKeluar->barang->nama . ' - ' . $barangKeluar->barang->merek }}</h5>
                        <div class="card-subtitle text-muted">
                            {{ $barangKeluar->barang->kode_barang }} <br>
                            {{ $barangKeluar->kode_barang }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <a href="{{ route('brg-keluar.index') }}">
                            <button class="btn btn-outline-secondary">
                                Kembali
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">
                                <i class="bx bx-download" style="position: relative; bottom: 2px;"></i>
                                Jumlah Barang Keluar
                            </span>
                            <h2 class="card-title mb-0 text-primary">{{ $barangKeluar->jumlah }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">
                                <i class="bx bx-calendar" style="position: relative; bottom: 2px;"></i>
                                Tanggal Keluar
                            </span>
                            <h3 class="card-title text-primary mb-3 mt-3">
                                {{ \Carbon\Carbon::parse($barangKeluar->tanggal_masuk)->translatedFormat('l, d F Y') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-8">
                    <div class="card">
                        <div class="card-body mb-3">
                            <span class="fw-semibold d-block mb-1">
                                <i class="bx bx-package" style="position: relative; bottom: 2px;"></i>
                                Kondisi Barang / Keterangan
                            </span>
                            <h6 class="card-title text-muted mb-1 mt-3">{{ $barangKeluar->keterangan }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    @endsection
