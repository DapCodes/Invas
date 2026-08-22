@extends('layouts.admin')
@section('page-title', 'Form Pengembalian Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-undo text-success me-2"></i>Tambah Transaksi Pengembalian</h5>
                <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('pengembalian.store') }}" method="POST" id="formPengembalian">
                    @csrf

                    {{-- 1. PILIH TRANSAKSI PEMINJAMAN --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="id_peminjam">Peminjaman <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="id_peminjam" id="id_peminjam" class="form-select @error('id_peminjam') is-invalid @enderror" required onchange="onLoanSelectChange()">
                                <option value="">-- Pilih Transaksi Peminjaman Aktif --</option>
                                @foreach ($activeLoans as $loan)
                                    <option value="{{ $loan->id }}"
                                        data-borrower="{{ $loan->nama_peminjam }}"
                                        data-barang="{{ $loan->barang?->nama }}"
                                        data-serial="{{ $loan->inventoryItem?->serial_number ?? 'Non-Serial' }}"
                                        data-satuan="{{ $loan->barang?->unit?->symbol ?? 'pcs' }}"
                                        data-total="{{ (float) $loan->jumlah }}"
                                        data-returned="{{ (float) $loan->pengembalian->sum('jumlah') }}"
                                        data-outstanding="{{ (float) $loan->outstanding }}"
                                        {{ (old('id_peminjam') == $loan->id || (isset($selectedPeminjaman) && $selectedPeminjaman->id == $loan->id)) ? 'selected' : '' }}>
                                        #{{ $loan->kode_barang }} - {{ $loan->nama_peminjam }} [{{ $loan->barang?->nama }} - {{ $loan->inventoryItem?->serial_number ?? 'Non-Serial' }}] (Sisa: {{ $loan->outstanding }} {{ $loan->barang?->unit?->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_peminjam')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- INFO CARD PEMINJAMAN TERPILIH --}}
                    <div class="row mb-3 d-none" id="loanSummaryCardRow">
                        <div class="col-sm-10 offset-sm-2">
                            <div class="card p-3 border border-success bg-label-success shadow-none">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Peminjam:</small>
                                        <strong id="displayBorrower" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Barang / Serial:</small>
                                        <strong id="displayBarangSerial" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Total Dipinjam:</small>
                                        <strong id="displayTotalBorrowed" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Belum Kembali (Max):</small>
                                        <strong id="displayOutstanding" class="text-danger fs-6">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. JUMLAH DIKEMBALIKAN --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="jumlah_kembali">Jumlah Kembali <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                <input name="jumlah_kembali" type="number" step="any" min="0.01" id="jumlah_kembali"
                                    class="form-control @error('jumlah_kembali') is-invalid @enderror"
                                    placeholder="0" value="{{ old('jumlah_kembali') }}" required oninput="validateReturnQty()" />
                                <span class="input-group-text return-satuan-label">pcs</span>
                            </div>
                            <small class="text-muted">Mendukung pengembalian bertahap/parsial.</small>
                            @error('jumlah_kembali')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_kembali">Tanggal Kembali <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input name="tanggal_kembali" type="date" id="tanggal_kembali" class="form-control"
                                    value="{{ old('tanggal_kembali', date('Y-m-d')) }}" required />
                            </div>
                        </div>
                    </div>

                    {{-- 3. KONDISI BARANG --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="kondisi">Kondisi Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="kondisi" id="kondisi" class="form-select @error('kondisi') is-invalid @enderror" required>
                                <option value="Baik" {{ old('kondisi') === 'Baik' ? 'selected' : '' }}>Baik (Normal / Siap Dipakai Lagi)</option>
                                <option value="Rusak" {{ old('kondisi') === 'Rusak' ? 'selected' : '' }}>Rusak (Damaged)</option>
                                <option value="Sebagian Rusak" {{ old('kondisi') === 'Sebagian Rusak' ? 'selected' : '' }}>Sebagian Rusak</option>
                                <option value="Hilang" {{ old('kondisi') === 'Hilang' ? 'selected' : '' }}>Hilang (Lost)</option>
                                <option value="Tidak Lengkap" {{ old('kondisi') === 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap / Aksesoris Kurang</option>
                            </select>
                            @error('kondisi')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label text-sm-end" for="ruangan_id">Lokasi Ruangan Masuk</label>
                        <div class="col-sm-4">
                            <select name="ruangan_id" id="ruangan_id" class="form-select">
                                <option value="">-- Pilih Ruangan Penyimpanan --</option>
                                @foreach ($ruangan as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 4. KETERANGAN --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="keterangan">Keterangan / Catatan Fisik</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-note"></i></span>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="2"
                                    placeholder="Catatan kondisi saat diterima kembali, kelengkapan, dll...">{{ old('keterangan', 'Barang dikembalikan dalam kondisi baik') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-success px-4" id="submitBtn">
                                <i class="bx bx-check-double me-1"></i> Simpan Pengembalian
                            </button>
                            <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let maxOutstanding = 0;

        function onLoanSelectChange() {
            const select = document.getElementById('id_peminjam');
            const selectedOpt = select.options[select.selectedIndex];
            const cardRow = document.getElementById('loanSummaryCardRow');
            const qtyInput = document.getElementById('jumlah_kembali');

            if (!selectedOpt.value) {
                cardRow.classList.add('d-none');
                maxOutstanding = 0;
                return;
            }

            const borrower = selectedOpt.dataset.borrower;
            const barang = selectedOpt.dataset.barang;
            const serial = selectedOpt.dataset.serial;
            const satuan = selectedOpt.dataset.satuan;
            const total = parseFloat(selectedOpt.dataset.total || 0);
            maxOutstanding = parseFloat(selectedOpt.dataset.outstanding || 0);

            document.getElementById('displayBorrower').innerText = borrower;
            document.getElementById('displayBarangSerial').innerText = `${barang} (${serial})`;
            document.getElementById('displayTotalBorrowed').innerText = `${total} ${satuan}`;
            document.getElementById('displayOutstanding').innerText = `${maxOutstanding} ${satuan}`;
            document.querySelectorAll('.return-satuan-label').forEach(el => el.innerText = satuan);

            cardRow.classList.remove('d-none');
            qtyInput.value = maxOutstanding;
            validateReturnQty();
        }

        function validateReturnQty() {
            const qtyInput = document.getElementById('jumlah_kembali');
            const val = parseFloat(qtyInput.value || 0);
            const submitBtn = document.getElementById('submitBtn');

            if (val > maxOutstanding || val <= 0) {
                qtyInput.classList.add('is-invalid');
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                qtyInput.classList.remove('is-invalid');
                submitBtn.removeAttribute('disabled');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            onLoanSelectChange();
        });
    </script>
@endsection
