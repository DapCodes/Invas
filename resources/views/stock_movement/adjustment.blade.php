@extends('layouts.admin')
@section('page-title', 'Koreksi Stok & Stock Opname')

@section('content')
    @include('sweetalert::alert')

    <div class="row">
        {{-- FORM STOCK ADJUSTMENT --}}
        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-slider-alt text-warning me-2"></i>Form Koreksi Stok (Opname)</h5>
                    <a href="{{ route('stock-movement.index') }}" class="btn btn-xs btn-outline-secondary">
                        <i class="bx bx-history me-1"></i> Buku Mutasi
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-adjustment.store') }}" method="POST" id="formAdjustment">
                        @csrf

                        {{-- Mode Selector --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipe Penyesuaian Barang <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_serialized" value="serialized" checked onchange="toggleAdjustMode()">
                                    <label class="form-check-label" for="type_serialized">
                                        Unit Serial Number / Kabel
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_non_serialized" value="non_serialized" onchange="toggleAdjustMode()">
                                    <label class="form-check-label" for="type_non_serialized">
                                        Barang Non-Serial
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Section A: Serialized --}}
                        <div id="adjustSerialSection">
                            <div class="mb-3">
                                <label class="form-label">Pilih Barang Serial <span class="text-danger">*</span></label>
                                <select id="adjBarangSerial" class="form-select" onchange="onAdjBarangSerialChange()">
                                    <option value="">-- Pilih Master Barang --</option>
                                    @foreach ($barangs->where('has_serial_number', true) as $b)
                                        <option value="{{ $b->id }}"
                                            data-satuan="{{ $b->unit?->symbol }}"
                                            data-serials="{{ json_encode($b->inventoryItems) }}">
                                            {{ $b->nama }} ({{ $b->merek }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pilih Unit Serial <span class="text-danger">*</span></label>
                                <select name="inventory_item_id" id="adjInventoryItemId" class="form-select" onchange="onAdjSerialSelect()">
                                    <option value="">-- Pilih Serial Number --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status Unit Baru</label>
                                <select name="new_status" id="adjNewStatus" class="form-select">
                                    <option value="available">Tersedia (Available)</option>
                                    <option value="in_use">Sedang Digunakan (In Use)</option>
                                    <option value="damaged">Rusak (Damaged)</option>
                                    <option value="lost">Hilang (Lost)</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="depleted">Habis Terpakai (Depleted)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Section B: Non-Serialized --}}
                        <div id="adjustNonSerialSection" class="d-none">
                            <div class="mb-3">
                                <label class="form-label">Pilih Barang Non-Serial <span class="text-danger">*</span></label>
                                <select name="barang_id" id="adjBarangNonSerial" class="form-select" onchange="onAdjBarangNonSerialChange()">
                                    <option value="">-- Pilih Barang Non-Serial --</option>
                                    @foreach ($barangs->where('has_serial_number', false) as $b)
                                        <option value="{{ $b->id }}"
                                            data-stok="{{ (float)$b->stok }}"
                                            data-satuan="{{ $b->unit?->symbol }}"
                                            data-rooms="{{ json_encode($b->barangRuangan) }}">
                                            {{ $b->nama }} ({{ $b->merek }}) - Total: {{ number_format((float)$b->stok, 2) }} {{ $b->unit?->symbol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pilih Lokasi Ruangan (Opsional)</label>
                                <select name="ruangan_id" id="adjRuanganId" class="form-select">
                                    <option value="">-- Seluruh Ruangan (Master) --</option>
                                    @foreach ($ruangans as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Quantity System vs Physical Comparison --}}
                        <div class="card p-3 bg-light border mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Stok Tercatat di Sistem:</small>
                                <strong id="adjSystemQtyDisplay" class="text-dark">0</strong>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Stok Fisik Sebenarnya (Hasil Opname) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="any" min="0" name="actual_quantity" id="adjActualQty" class="form-control" placeholder="0" required oninput="calcAdjDelta()" />
                                    <span class="input-group-text adj-satuan-label">pcs</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Selisih Penyesuaian (Delta):</small>
                                <strong id="adjDeltaDisplay" class="fs-6 text-muted">0</strong>
                            </div>
                        </div>

                        {{-- Alasan / Keterangan --}}
                        <div class="mb-3">
                            <label class="form-label">Alasan Koreksi / Opname <span class="text-danger">*</span></label>
                            <textarea name="alasan" id="adjAlasan" class="form-control" rows="2" placeholder="Contoh: Hasil stock opname akhir bulan / kabel terpotong" required></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-warning" id="adjSubmitBtn">
                                <i class="bx bx-save me-1"></i> Simpan Penyesuaian Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- AUDIT TRAIL KOREKSI TERAKHIR --}}
        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-history text-info me-2"></i>Riwayat Koreksi Stok Terkini</h5>
                    <a href="{{ route('stock-movement.index', ['type' => 'adjustment']) }}" class="btn btn-xs btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped align-middle table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Barang / Serial</th>
                                <th>Penyesuaian</th>
                                <th>Saldo Akhir</th>
                                <th>Petugas</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentAdjustments as $adj)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $adj->tanggal?->translatedFormat('d M Y') }}</div>
                                        <small class="text-muted">{{ $adj->tanggal?->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $adj->barang?->nama }}</strong>
                                        @if ($adj->inventoryItem)
                                            <div><small class="text-primary"><code>{{ $adj->inventoryItem->serial_number }}</code></small></div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ (float)$adj->quantity > 0 ? 'bg-label-success' : ((float)$adj->quantity < 0 ? 'bg-label-danger' : 'bg-label-secondary') }}">
                                            {{ (float)$adj->quantity > 0 ? '+' : '' }}{{ number_format((float)$adj->quantity, 2) }} {{ $adj->barang?->unit?->symbol }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format((float)$adj->quantity_after, 2) }}</strong>
                                    </td>
                                    <td>{{ $adj->user?->name ?? 'Sistem' }}</td>
                                    <td><small class="text-muted">{{ Str::limit($adj->keterangan, 30) }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat penyesuaian stok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSystemStock = 0;

        function toggleAdjustMode() {
            const isSerial = document.getElementById('type_serialized').checked;
            const serialSec = document.getElementById('adjustSerialSection');
            const nonSerialSec = document.getElementById('adjustNonSerialSection');

            if (isSerial) {
                serialSec.classList.remove('d-none');
                nonSerialSec.classList.add('d-none');
                onAdjBarangSerialChange();
            } else {
                serialSec.classList.add('d-none');
                nonSerialSec.classList.remove('d-none');
                onAdjBarangNonSerialChange();
            }
        }

        function onAdjBarangSerialChange() {
            const select = document.getElementById('adjBarangSerial');
            const selectedOpt = select.options[select.selectedIndex];
            const itemSelect = document.getElementById('adjInventoryItemId');

            if (!selectedOpt || !selectedOpt.value) {
                itemSelect.innerHTML = '<option value="">-- Pilih Serial Number --</option>';
                currentSystemStock = 0;
                calcAdjDelta();
                return;
            }

            const satuan = selectedOpt.dataset.satuan || 'pcs';
            document.querySelectorAll('.adj-satuan-label').forEach(el => el.innerText = satuan);

            let serials = [];
            try {
                serials = JSON.parse(selectedOpt.dataset.serials || '[]');
            } catch(e) {}

            itemSelect.innerHTML = '<option value="">-- Pilih Serial Number --</option>';
            serials.forEach(item => {
                itemSelect.innerHTML += `<option value="${item.id}" data-qty="${item.current_quantity}" data-status="${item.status}">
                    ${item.serial_number} (Stok: ${item.current_quantity} ${satuan}, Status: ${item.status})
                </option>`;
            });

            currentSystemStock = 0;
            calcAdjDelta();
        }

        function onAdjSerialSelect() {
            const itemSelect = document.getElementById('adjInventoryItemId');
            const opt = itemSelect.options[itemSelect.selectedIndex];

            if (opt && opt.value) {
                currentSystemStock = parseFloat(opt.dataset.qty || 0);
                document.getElementById('adjActualQty').value = currentSystemStock;
                document.getElementById('adjNewStatus').value = opt.dataset.status || 'available';
            } else {
                currentSystemStock = 0;
            }

            calcAdjDelta();
        }

        function onAdjBarangNonSerialChange() {
            const select = document.getElementById('adjBarangNonSerial');
            const selectedOpt = select.options[select.selectedIndex];

            if (!selectedOpt || !selectedOpt.value) {
                currentSystemStock = 0;
                calcAdjDelta();
                return;
            }

            const satuan = selectedOpt.dataset.satuan || 'pcs';
            document.querySelectorAll('.adj-satuan-label').forEach(el => el.innerText = satuan);

            currentSystemStock = parseFloat(selectedOpt.dataset.stok || 0);
            document.getElementById('adjActualQty').value = currentSystemStock;
            calcAdjDelta();
        }

        function calcAdjDelta() {
            const actual = parseFloat(document.getElementById('adjActualQty').value || 0);
            const delta = actual - currentSystemStock;

            document.getElementById('adjSystemQtyDisplay').innerText = currentSystemStock.toLocaleString('id-ID');
            const deltaDisplay = document.getElementById('adjDeltaDisplay');

            if (delta > 0) {
                deltaDisplay.innerText = '+' + delta.toLocaleString('id-ID') + ' (Stok Bertambah)';
                deltaDisplay.className = 'fs-6 text-success fw-bold';
            } else if (delta < 0) {
                deltaDisplay.innerText = delta.toLocaleString('id-ID') + ' (Stok Berkurang)';
                deltaDisplay.className = 'fs-6 text-danger fw-bold';
            } else {
                deltaDisplay.innerText = '0 (Tidak Ada Selisih)';
                deltaDisplay.className = 'fs-6 text-muted';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleAdjustMode();
        });
    </script>
@endsection
