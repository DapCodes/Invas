@extends('layouts.admin')
@section('page-title', 'Tambah Master Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah Data Master Barang</h5>
                <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" id="formMasterBarang">
                    @csrf

                    {{-- INFORMASI DASAR --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-info-circle me-1"></i> 1. Informasi Utama Barang</h6>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama">Nama Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-collection"></i></span>
                                <input name="nama" type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                    placeholder="Contoh: iPhone 17 Pro / Kabel Fiber Optik / Kursi Kantor" value="{{ old('nama') }}" required />
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
                                    placeholder="Contoh: Apple / Belden / Chitose" value="{{ old('merek') }}" required />
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
                                    <option value="">-- Pilih Satuan Standar --</option>
                                    @foreach ($units as $u)
                                        <option value="{{ $u->id }}" {{ old('satuan_id') == $u->id ? 'selected' : '' }}>
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
                                        <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
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
                        <label class="col-sm-2 col-form-label" for="deskripsi">Deskripsi Barang</label>
                        <div class="col-sm-10">
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2"
                                placeholder="Keterangan spesifikasi barang, catatan garansi, dll.">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="foto">Foto Barang</label>
                        <div class="col-sm-10">
                            <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*" />
                            <small class="text-muted">Format: jpg, jpeg, png, webp (Maks. 2MB)</small>
                            @error('foto')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- TIPE INVENTARIS: SERIAL VS NON-SERIAL --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-cog me-1"></i> 2. Konfigurasi Serial Number & Stok Awal</h6>

                    <div class="row mb-4">
                        <div class="col-sm-10 offset-sm-2">
                            <div class="card p-3 border border-primary bg-label-primary">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="has_serial_number" name="has_serial_number" value="1"
                                        {{ old('has_serial_number') ? 'checked' : '' }} onchange="toggleSerialMode()">
                                    <label class="form-check-label fw-bold text-dark fs-6" for="has_serial_number">
                                        Barang memiliki Serial Number / Sub-Barang Individual
                                    </label>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    Centang opsi ini jika barang memiliki nomor seri unik per unit (contoh: Laptop, iPhone, Switch) atau nomor seri gulungan/drum dengan kuantitas (contoh: Kabel Fiber Optik).
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2A: NON-SERIALIZED INITIAL STOCK --}}
                    <div id="nonSerialSection" class="{{ old('has_serial_number') ? 'd-none' : '' }}">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="stok">Stok Awal (Opsional)</label>
                            <div class="col-sm-4">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                    <input name="stok" type="number" step="any" min="0" id="stok" class="form-control"
                                        placeholder="0" value="{{ old('stok', 0) }}" />
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-end" for="ruangan_id">Ruangan / Lokasi</label>
                            <div class="col-sm-4">
                                <select name="ruangan_id" id="ruangan_id" class="form-select">
                                    <option value="">-- Pilih Ruangan Awal --</option>
                                    @foreach ($ruangans as $r)
                                        <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2B: SERIALIZED INITIAL ITEMS --}}
                    <div id="serialSection" class="{{ old('has_serial_number') ? '' : 'd-none' }}">
                        <div class="card border border-secondary shadow-none mb-3">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold"><i class="bx bx-list-ol me-1"></i> Daftar Unit Serial Number Awal</span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#pasteMultipleArea">
                                        <i class="bx bx-paste me-1"></i> Paste Banyak Serial
                                    </button>
                                    <button type="button" class="btn btn-xs btn-success" onclick="addSerialRow()">
                                        <i class="bx bx-plus me-1"></i> Tambah Baris
                                    </button>
                                </div>
                            </div>

                            {{-- Collapse paste text area --}}
                            <div class="collapse p-3 border-bottom bg-lighter" id="pasteMultipleArea">
                                <label class="form-label small fw-bold">Tempelkan Banyak Serial (1 baris per Serial Number):</label>
                                <textarea id="bulkSerialInput" class="form-control form-control-sm mb-2" rows="3" placeholder="SN001&#10;SN002&#10;SN003"></textarea>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="applyBulkSerials()">
                                        <i class="bx bx-check"></i> Generate Baris
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#pasteMultipleArea">
                                        Tutup
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle" id="serialTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Nomor Seri (Serial Number) <span class="text-danger">*</span></th>
                                                <th style="width: 150px;">Kuantitas (Qty)</th>
                                                <th style="width: 250px;">Lokasi / Ruangan</th>
                                                <th style="width: 50px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="serialTableBody">
                                            {{-- Rows dynamically appended --}}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-2 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSerialRow()">
                                        <i class="bx bx-plus me-1"></i> Tambah Unit Serial
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Simpan Master Barang
                            </button>
                            <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let serialRowIndex = 0;
        const ruangansList = @json($ruangans);

        function toggleSerialMode() {
            const isSerial = document.getElementById('has_serial_number').checked;
            const nonSerialSec = document.getElementById('nonSerialSection');
            const serialSec = document.getElementById('serialSection');

            if (isSerial) {
                nonSerialSec.classList.add('d-none');
                serialSec.classList.remove('d-none');
                if (document.querySelectorAll('#serialTableBody tr').length === 0) {
                    addSerialRow();
                }
            } else {
                nonSerialSec.classList.remove('d-none');
                serialSec.classList.add('d-none');
            }
        }

        function addSerialRow(serialValue = '', qtyValue = 1, ruanganId = '') {
            const tbody = document.getElementById('serialTableBody');
            const rowNumber = tbody.children.length + 1;

            let ruanganOptions = '<option value="">-- Pilih Ruangan --</option>';
            ruangansList.forEach(r => {
                ruanganOptions += `<option value="${r.id}" ${ruanganId == r.id ? 'selected' : ''}>${r.nama_ruangan}</option>`;
            });

            const tr = document.createElement('tr');
            tr.id = `serialRow_${serialRowIndex}`;
            tr.innerHTML = `
                <td class="text-center serial-index">${rowNumber}</td>
                <td>
                    <input type="text" name="serials[${serialRowIndex}][serial_number]" class="form-control form-control-sm"
                        placeholder="Contoh: SN-001 / FO-2026-01" value="${serialValue}" required />
                </td>
                <td>
                    <input type="number" step="any" min="0.01" name="serials[${serialRowIndex}][quantity]" class="form-control form-control-sm"
                        placeholder="1.00" value="${qtyValue}" required />
                </td>
                <td>
                    <select name="serials[${serialRowIndex}][ruangan_id]" class="form-select form-select-sm">
                        ${ruanganOptions}
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeSerialRow('serialRow_${serialRowIndex}')">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            serialRowIndex++;
            reindexSerialRows();
        }

        function removeSerialRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                reindexSerialRows();
            }
        }

        function reindexSerialRows() {
            const rows = document.querySelectorAll('#serialTableBody tr');
            rows.forEach((r, idx) => {
                r.querySelector('.serial-index').innerText = idx + 1;
            });
        }

        function applyBulkSerials() {
            const text = document.getElementById('bulkSerialInput').value.trim();
            if (!text) return;

            const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
            lines.forEach(sn => {
                addSerialRow(sn, 1);
            });

            document.getElementById('bulkSerialInput').value = '';
            const collapseElem = document.getElementById('pasteMultipleArea');
            const bsCollapse = bootstrap.Collapse.getInstance(collapseElem);
            if (bsCollapse) bsCollapse.hide();
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('has_serial_number').checked) {
                toggleSerialMode();
            }
        });
    </script>
@endsection
