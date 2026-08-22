@extends('layouts.admin')
@section('page-title', 'Pengeluaran Barang Keluar')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-export text-danger me-2"></i>Tambah Data Pengeluaran Barang Keluar</h5>
                <a href="{{ route('brg-keluar.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('brg-keluar.store') }}" method="POST" id="formBarangKeluar">
                    @csrf

                    {{-- 1. PILIH BARANG --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="id_barang">Pilih Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="id_barang" id="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required onchange="onBarangKeluarChange()">
                                <option value="">-- Pilih Master Barang --</option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id }}"
                                        data-serial="{{ $item->has_serial_number ? '1' : '0' }}"
                                        data-satuan="{{ $item->unit?->symbol ?? 'pcs' }}"
                                        data-is-decimal="{{ $item->unit?->is_decimal ? '1' : '0' }}"
                                        data-stok="{{ (float) $item->stok }}"
                                        data-serials="{{ json_encode($item->inventoryItems) }}"
                                        {{ old('id_barang') == $item->id ? 'selected' : '' }}>
                                        {{ $item->kode_barang }} - {{ $item->nama }} ({{ $item->merek }}) [Stok Total: {{ number_format((float)$item->stok, $item->unit?->is_decimal ? 2 : 0) }} {{ $item->unit?->symbol }}]
                                    </option>
                                @endforeach
                            </select>
                            <div id="barangBadgeArea" class="mt-2"></div>
                            @error('id_barang')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. TANGGAL KELUAR --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="tanggal_keluar">Tanggal Keluar <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input name="tanggal_keluar" type="date" id="tanggal_keluar" class="form-control"
                                    value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required />
                            </div>
                            @error('tanggal_keluar')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- SECTION A: SERIALIZED UNIT / CABLE SELECTION --}}
                    <div id="serialKeluarSection" class="d-none">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="inventory_item_id">Pilih Unit Serial <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="inventory_item_id" id="inventory_item_id" class="form-select @error('inventory_item_id') is-invalid @enderror" onchange="onSerialSelectChange()">
                                    <option value="">-- Pilih Nomor Seri Unit --</option>
                                </select>
                                @error('inventory_item_id')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECTION B: NON-SERIAL ROOM SELECTION --}}
                    <div id="nonSerialRoomSection" class="d-none">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="ruangan_id">Ambil Dari Ruangan</label>
                            <div class="col-sm-10">
                                <select name="ruangan_id" id="ruangan_id" class="form-select">
                                    <option value="">-- Pilih Ruangan Asal --</option>
                                    @foreach ($ruangan as $r)
                                        <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 3. QUANTITY & REALTIME SALDO CALCULATOR --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="jumlah">Jumlah Keluar <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                <input name="jumlah" type="number" step="any" min="0.01" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                                    placeholder="0" value="{{ old('jumlah') }}" required oninput="calculateRemainingStock()" />
                                <span class="input-group-text satuan-label">pcs</span>
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            {{-- Realtime stock calculation card --}}
                            <div class="card p-2 border bg-light shadow-none" id="stockPreviewCard">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">Stok Tersedia:</small>
                                        <strong id="availableStockDisplay" class="text-primary fs-6">0</strong> <span class="satuan-label">pcs</span>
                                    </div>
                                    <div class="text-center">
                                        <i class="bx bx-right-arrow-alt fs-4 text-muted"></i>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Estimasi Sisa Stok:</small>
                                        <strong id="remainingStockDisplay" class="text-success fs-6">0</strong> <span class="satuan-label">pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. KETERANGAN FLEKSIBEL --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="keterangan">Keterangan / Keperluan <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-note"></i></span>
                                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                    rows="2" placeholder="Contoh: Digunakan untuk pemasangan jaringan pelanggan / Dibawa teknisi Budi / Rusak / Proyek A" required>{{ old('keterangan') }}</textarea>
                            </div>
                            <small class="text-muted">Masukkan alasan pengeluaran secara fleksibel (digunakan, proyek, dibawa teknisi, rusak, dll.).</small>
                            @error('keterangan')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-danger px-4" id="submitBtn">
                                <i class="bx bx-export me-1"></i> Simpan Barang Keluar
                            </button>
                            <a href="{{ route('brg-keluar.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentAvailable = 0;

        function onBarangKeluarChange() {
            const select = document.getElementById('id_barang');
            const selectedOpt = select.options[select.selectedIndex];

            const serialSec = document.getElementById('serialKeluarSection');
            const nonSerialRoomSec = document.getElementById('nonSerialRoomSection');
            const serialSelect = document.getElementById('inventory_item_id');
            const badgeArea = document.getElementById('barangBadgeArea');
            const jumlahInput = document.getElementById('jumlah');

            if (!selectedOpt.value) {
                serialSec.classList.add('d-none');
                nonSerialRoomSec.classList.add('d-none');
                badgeArea.innerHTML = '';
                currentAvailable = 0;
                calculateRemainingStock();
                return;
            }

            const isSerial = selectedOpt.dataset.serial === '1';
            const satuan = selectedOpt.dataset.satuan || 'pcs';
            const masterStok = parseFloat(selectedOpt.dataset.stok || 0);

            document.querySelectorAll('.satuan-label').forEach(el => el.innerText = satuan);

            if (isSerial) {
                badgeArea.innerHTML = `<span class="badge bg-info"><i class="bx bx-barcode me-1"></i> Serialized</span> <span class="badge bg-label-dark ms-1">Satuan: ${satuan}</span>`;
                serialSec.classList.remove('d-none');
                nonSerialRoomSec.classList.add('d-none');
                serialSelect.setAttribute('required', 'required');

                // Populate serial options
                let rawSerials = selectedOpt.dataset.serials;
                let serials = [];
                try {
                    serials = JSON.parse(rawSerials);
                } catch(e) {}

                serialSelect.innerHTML = '<option value="">-- Pilih Nomor Seri Unit --</option>';
                serials.forEach(item => {
                    const roomName = item.ruangan ? item.ruangan.nama_ruangan : 'Gudang';
                    serialSelect.innerHTML += `<option value="${item.id}" data-qty="${item.current_quantity}" data-room="${roomName}">
                        ${item.serial_number} — Sisa: ${item.current_quantity} ${satuan} (${roomName})
                    </option>`;
                });

                currentAvailable = 0;
                jumlahInput.value = '';
            } else {
                badgeArea.innerHTML = `<span class="badge bg-secondary"><i class="bx bx-cube me-1"></i> Non-Serial</span> <span class="badge bg-label-dark ms-1">Satuan: ${satuan}</span>`;
                serialSec.classList.add('d-none');
                nonSerialRoomSec.classList.remove('d-none');
                serialSelect.removeAttribute('required');

                currentAvailable = masterStok;
            }

            calculateRemainingStock();
        }

        function onSerialSelectChange() {
            const serialSelect = document.getElementById('inventory_item_id');
            const selectedOpt = serialSelect.options[serialSelect.selectedIndex];
            const jumlahInput = document.getElementById('jumlah');

            if (selectedOpt && selectedOpt.value) {
                currentAvailable = parseFloat(selectedOpt.dataset.qty || 0);
                if (currentAvailable == 1) {
                    jumlahInput.value = 1;
                }
            } else {
                currentAvailable = 0;
            }

            calculateRemainingStock();
        }

        function calculateRemainingStock() {
            const jumlahInput = document.getElementById('jumlah');
            const qtyOut = parseFloat(jumlahInput.value || 0);
            const remaining = currentAvailable - qtyOut;

            document.getElementById('availableStockDisplay').innerText = currentAvailable.toLocaleString('id-ID');
            const remainingDisplay = document.getElementById('remainingStockDisplay');
            const submitBtn = document.getElementById('submitBtn');

            if (qtyOut > 0 && remaining >= 0) {
                remainingDisplay.innerText = remaining.toLocaleString('id-ID');
                remainingDisplay.className = 'text-success fs-6';
                submitBtn.removeAttribute('disabled');
            } else if (remaining < 0) {
                remainingDisplay.innerText = 'Stok Tidak Cukup! (' + remaining.toLocaleString('id-ID') + ')';
                remainingDisplay.className = 'text-danger fs-6 fw-bold';
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                remainingDisplay.innerText = currentAvailable.toLocaleString('id-ID');
                remainingDisplay.className = 'text-success fs-6';
                submitBtn.removeAttribute('disabled');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            onBarangKeluarChange();
        });
    </script>
@endsection
