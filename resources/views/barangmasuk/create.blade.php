@extends('layouts.admin')
@section('page-title', 'Penerimaan Barang Masuk')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-import text-primary me-2"></i>Tambah Data Penerimaan Barang Masuk</h5>
                <a href="{{ route('brg-masuk.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('brg-masuk.store') }}" method="POST" id="formBarangMasuk">
                    @csrf

                    {{-- 1. PILIH BARANG --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="id_barang">Pilih Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="id_barang" id="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required onchange="onBarangChange()">
                                <option value="">-- Pilih Master Barang --</option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id }}"
                                        data-serial="{{ $item->has_serial_number ? '1' : '0' }}"
                                        data-satuan="{{ $item->unit?->symbol ?? 'pcs' }}"
                                        data-is-decimal="{{ $item->unit?->is_decimal ? '1' : '0' }}"
                                        data-serials="{{ json_encode($item->inventoryItems) }}"
                                        {{ old('id_barang') == $item->id ? 'selected' : '' }}>
                                        {{ $item->kode_barang }} - {{ $item->nama }} ({{ $item->merek }}) [{{ $item->has_serial_number ? 'SERIALIZED' : 'NON-SERIAL' }}]
                                    </option>
                                @endforeach
                            </select>
                            <div id="barangBadgeArea" class="mt-2"></div>
                            @error('id_barang')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. TANGGAL MASUK & RUANGAN DEFAULT --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input name="tanggal_masuk" type="date" id="tanggal_masuk" class="form-control"
                                    value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required />
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label text-sm-end" for="ruangan_id">Lokasi / Ruangan</label>
                        <div class="col-sm-4">
                            <select name="ruangan_id" id="ruangan_id" class="form-select">
                                <option value="">-- Pilih Ruangan Default --</option>
                                @foreach ($ruangan as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- SECTION A: NON-SERIAL QUANTITY INPUT --}}
                    <div id="nonSerialSection" class="d-none">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jumlah">Jumlah Masuk <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                    <input name="jumlah" type="number" step="any" min="0.01" id="jumlah" class="form-control"
                                        placeholder="0" value="{{ old('jumlah') }}" />
                                    <span class="input-group-text satuan-label">pcs</span>
                                </div>
                                <small class="text-muted">Stok master dan stok ruangan akan otomatis bertambah.</small>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION B: SERIALIZED MULTI-ROW REPEATER --}}
                    <div id="serialSection" class="d-none">
                        <div class="card border border-primary shadow-none mb-3">
                            <div class="card-header bg-label-primary py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary"><i class="bx bx-barcode me-1"></i> Input Unit Serial Number Penerimaan</span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#pasteMultipleArea">
                                        <i class="bx bx-paste me-1"></i> Paste Banyak Serial
                                    </button>
                                    <button type="button" class="btn btn-xs btn-primary" onclick="addSerialRow()">
                                        <i class="bx bx-plus me-1"></i> Tambah Baris
                                    </button>
                                </div>
                            </div>

                            {{-- Collapse paste area --}}
                            <div class="collapse p-3 border-bottom bg-light" id="pasteMultipleArea">
                                <label class="form-label small fw-bold">Tempelkan Daftar Serial Number (1 baris per unit):</label>
                                <textarea id="bulkSerialInput" class="form-control form-control-sm mb-2" rows="3" placeholder="SN-001&#10;SN-002&#10;SN-003"></textarea>
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
                                                <th style="width: 160px;">Kuantitas (<span class="satuan-label">unit</span>)</th>
                                                <th style="width: 250px;">Lokasi Ruangan</th>
                                                <th style="width: 50px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="serialTableBody">
                                            {{-- Rows dynamically generated --}}
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-2 d-flex justify-content-between align-items-center">
                                    <div id="existingSerialQuickSelect" class="d-flex align-items-center gap-2">
                                        {{-- Dropdown for existing cable serials --}}
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSerialRow()">
                                        <i class="bx bx-plus me-1"></i> Tambah Baris Serial
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. KETERANGAN --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="keterangan">Keterangan <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-note"></i></span>
                                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                    rows="2" placeholder="Contoh: Pengadaan PO-2026-08 / Pengembalian sisa material proyek" required>{{ old('keterangan', 'Penerimaan barang baru') }}</textarea>
                            </div>
                            @error('keterangan')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Simpan Barang Masuk
                            </button>
                            <a href="{{ route('brg-masuk.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let serialRowIndex = 0;
        const ruangansList = @json($ruangan);

        function onBarangChange() {
            const select = document.getElementById('id_barang');
            const selectedOpt = select.options[select.selectedIndex];

            const nonSerialSec = document.getElementById('nonSerialSection');
            const serialSec = document.getElementById('serialSection');
            const badgeArea = document.getElementById('barangBadgeArea');
            const jumlahInput = document.getElementById('jumlah');

            if (!selectedOpt.value) {
                nonSerialSec.classList.add('d-none');
                serialSec.classList.add('d-none');
                badgeArea.innerHTML = '';
                return;
            }

            const isSerial = selectedOpt.dataset.serial === '1';
            const satuan = selectedOpt.dataset.satuan || 'pcs';

            // Update satuan labels
            document.querySelectorAll('.satuan-label').forEach(el => el.innerText = satuan);

            if (isSerial) {
                badgeArea.innerHTML = `<span class="badge bg-info"><i class="bx bx-barcode me-1"></i> Barang Serialized</span> <span class="badge bg-label-dark ms-1">Satuan: ${satuan}</span>`;
                nonSerialSec.classList.add('d-none');
                serialSec.classList.remove('d-none');
                jumlahInput.removeAttribute('required');

                // Render existing serials helper
                const rawSerials = selectedOpt.dataset.serials;
                let existingSerials = [];
                try {
                    existingSerials = JSON.parse(rawSerials);
                } catch(e) {}

                const quickSelectDiv = document.getElementById('existingSerialQuickSelect');
                if (existingSerials.length > 0) {
                    let optHtml = '<option value="">-- Tambah Sisa/Top-up Serial Eksisting --</option>';
                    existingSerials.forEach(item => {
                        optHtml += `<option value="${item.serial_number}" data-qty="${item.current_quantity}" data-room="${item.ruangan_id || ''}">${item.serial_number} (Sisa: ${item.current_quantity} ${satuan})</option>`;
                    });
                    quickSelectDiv.innerHTML = `
                        <select class="form-select form-select-sm" style="max-width: 280px;" onchange="addExistingSerial(this)">
                            ${optHtml}
                        </select>
                    `;
                } else {
                    quickSelectDiv.innerHTML = '';
                }

                if (document.querySelectorAll('#serialTableBody tr').length === 0) {
                    addSerialRow();
                }
            } else {
                badgeArea.innerHTML = `<span class="badge bg-secondary"><i class="bx bx-cube me-1"></i> Barang Non-Serial</span> <span class="badge bg-label-dark ms-1">Satuan: ${satuan}</span>`;
                nonSerialSec.classList.remove('d-none');
                serialSec.classList.add('d-none');
                jumlahInput.setAttribute('required', 'required');
            }
        }

        function addSerialRow(serialValue = '', qtyValue = 1, ruanganId = '') {
            const tbody = document.getElementById('serialTableBody');
            const rowNumber = tbody.children.length + 1;
            const defaultRoom = document.getElementById('ruangan_id').value;

            let finalRoomId = ruanganId || defaultRoom;

            let ruanganOptions = '<option value="">-- Pilih Ruangan --</option>';
            ruangansList.forEach(r => {
                ruanganOptions += `<option value="${r.id}" ${finalRoomId == r.id ? 'selected' : ''}>${r.nama_ruangan}</option>`;
            });

            const tr = document.createElement('tr');
            tr.id = `bmSerialRow_${serialRowIndex}`;
            tr.innerHTML = `
                <td class="text-center bm-serial-index">${rowNumber}</td>
                <td>
                    <input type="text" name="serials[${serialRowIndex}][serial_number]" class="form-control form-control-sm"
                        placeholder="Contoh: SN-001 / FO-001" value="${serialValue}" required />
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
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeSerialRow('bmSerialRow_${serialRowIndex}')">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            serialRowIndex++;
            reindexSerialRows();
        }

        function addExistingSerial(selectElem) {
            const selectedOpt = selectElem.options[selectElem.selectedIndex];
            if (!selectedOpt.value) return;

            const sn = selectedOpt.value;
            const roomId = selectedOpt.dataset.room;
            addSerialRow(sn, 1, roomId);
            selectElem.selectedIndex = 0;
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
                r.querySelector('.bm-serial-index').innerText = idx + 1;
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

        document.addEventListener('DOMContentLoaded', function () {
            onBarangChange();
        });
    </script>
@endsection
