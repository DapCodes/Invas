@extends('layouts.admin')
@section('page-title', 'Form Peminjaman Barang')

@section('content')
    @include('sweetalert::alert')

    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-calendar-event text-warning me-2"></i>Tambah Transaksi Peminjaman</h5>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('peminjaman.store') }}" method="POST" id="formPeminjaman">
                    @csrf

                    {{-- 1. PILIH BARANG --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="id_barang">Pilih Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="id_barang" id="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required onchange="onBarangPinjamChange()">
                                <option value="">-- Pilih Master Barang --</option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id }}"
                                        data-serial="{{ $item->has_serial_number ? '1' : '0' }}"
                                        data-satuan="{{ $item->unit?->symbol ?? 'pcs' }}"
                                        data-stok="{{ (float) $item->stok }}"
                                        data-serials="{{ json_encode($item->inventoryItems) }}"
                                        {{ old('id_barang') == $item->id ? 'selected' : '' }}>
                                        {{ $item->kode_barang }} - {{ $item->nama }} ({{ $item->merek }}) [Tersedia: {{ number_format((float)$item->stok, $item->unit?->is_decimal ? 2 : 0) }} {{ $item->unit?->symbol }}]
                                    </option>
                                @endforeach
                            </select>
                            <div id="barangBadgeArea" class="mt-2"></div>
                            @error('id_barang')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- SECTION A: SERIALIZED UNIT SELECTION --}}
                    <div id="serialSection" class="d-none">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="inventory_item_id">Unit Serial Number <span class="text-danger">*</span></label>
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
                            <label class="col-sm-2 col-form-label" for="ruangan_id">Lokasi / Ruangan Asal</label>
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

                    {{-- 2. QUANTITY --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="jumlah">Jumlah Pinjam <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                <input name="jumlah" type="number" step="any" min="0.01" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                                    placeholder="0" value="{{ old('jumlah', 1) }}" required oninput="checkStockLimit()" />
                                <span class="input-group-text satuan-label">pcs</span>
                            </div>
                            <small class="text-muted" id="stokTersediaHelp">Stok tersedia: <span id="maxStokDisplay">0</span> <span class="satuan-label">pcs</span></small>
                            @error('jumlah')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- 3. NAMA PEMINJAM --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama_peminjam">Nama Peminjam <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input name="nama_peminjam" type="text" id="nama_peminjam" class="form-control @error('nama_peminjam') is-invalid @enderror"
                                    placeholder="Nama karyawan / peminjam / pihak peminjam" value="{{ old('nama_peminjam') }}" required />
                            </div>
                            @error('nama_peminjam')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- 4. TANGGAL PINJAM & RENCANA KEMBALI --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="tanggal_pinjam">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input name="tanggal_pinjam" type="date" id="tanggal_pinjam" class="form-control"
                                    value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required />
                            </div>
                        </div>

                        <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_kembali">Rencana Kembali <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar-check"></i></span>
                                <input name="tanggal_kembali" type="date" id="tanggal_kembali" class="form-control"
                                    value="{{ old('tanggal_kembali', date('Y-m-d', strtotime('+7 days'))) }}" required />
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-warning px-4" id="submitBtn">
                                <i class="bx bx-check-circle me-1"></i> Simpan Transaksi Peminjaman
                            </button>
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let availableQty = 0;

        function onBarangPinjamChange() {
            const select = document.getElementById('id_barang');
            const selectedOpt = select.options[select.selectedIndex];

            const serialSec = document.getElementById('serialSection');
            const nonSerialRoomSec = document.getElementById('nonSerialRoomSection');
            const serialSelect = document.getElementById('inventory_item_id');
            const badgeArea = document.getElementById('barangBadgeArea');
            const jumlahInput = document.getElementById('jumlah');

            if (!selectedOpt.value) {
                serialSec.classList.add('d-none');
                nonSerialRoomSec.classList.add('d-none');
                badgeArea.innerHTML = '';
                availableQty = 0;
                checkStockLimit();
                return;
            }

            const isSerial = selectedOpt.dataset.serial === '1';
            const satuan = selectedOpt.dataset.satuan || 'pcs';
            const masterStok = parseFloat(selectedOpt.dataset.stok || 0);

            document.querySelectorAll('.satuan-label').forEach(el => el.innerText = satuan);

            if (isSerial) {
                badgeArea.innerHTML = `<span class="badge bg-info"><i class="bx bx-barcode me-1"></i> Serialized</span>`;
                serialSec.classList.remove('d-none');
                nonSerialRoomSec.classList.add('d-none');
                serialSelect.setAttribute('required', 'required');

                let rawSerials = selectedOpt.dataset.serials;
                let serials = [];
                try {
                    serials = JSON.parse(rawSerials);
                } catch(e) {}

                serialSelect.innerHTML = '<option value="">-- Pilih Nomor Seri Unit --</option>';
                serials.forEach(item => {
                    serialSelect.innerHTML += `<option value="${item.id}" data-qty="${item.current_quantity}">
                        ${item.serial_number} (Tersedia: ${item.current_quantity} ${satuan})
                    </option>`;
                });

                availableQty = 0;
                jumlahInput.value = 1;
            } else {
                badgeArea.innerHTML = `<span class="badge bg-secondary"><i class="bx bx-cube me-1"></i> Non-Serial</span>`;
                serialSec.classList.add('d-none');
                nonSerialRoomSec.classList.remove('d-none');
                serialSelect.removeAttribute('required');

                availableQty = masterStok;
            }

            checkStockLimit();
        }

        function onSerialSelectChange() {
            const serialSelect = document.getElementById('inventory_item_id');
            const selectedOpt = serialSelect.options[serialSelect.selectedIndex];
            const jumlahInput = document.getElementById('jumlah');

            if (selectedOpt && selectedOpt.value) {
                availableQty = parseFloat(selectedOpt.dataset.qty || 0);
                if (availableQty == 1) {
                    jumlahInput.value = 1;
                }
            } else {
                availableQty = 0;
            }

            checkStockLimit();
        }

        function checkStockLimit() {
            const jumlahInput = document.getElementById('jumlah');
            const qty = parseFloat(jumlahInput.value || 0);
            const display = document.getElementById('maxStokDisplay');
            const submitBtn = document.getElementById('submitBtn');

            display.innerText = availableQty.toLocaleString('id-ID');

            if (qty > availableQty || qty <= 0) {
                display.className = 'text-danger fw-bold';
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                display.className = 'text-success fw-bold';
                submitBtn.removeAttribute('disabled');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            onBarangPinjamChange();
        });
    </script>
@endsection
