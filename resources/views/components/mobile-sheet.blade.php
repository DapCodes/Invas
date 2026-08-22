{{-- Mobile Draggable Bottom Sheet Modal (Complete App Navigation Launcher) --}}
<div class="mobile-sheet-backdrop d-lg-none" id="mobileSheetBackdrop" onclick="closeMobileMenuSheet()"></div>

<div class="mobile-sheet-modal d-lg-none" id="mobileMenuSheet" role="dialog" aria-modal="true" aria-labelledby="mobileSheetTitle">
    {{-- Drag Handle Bar --}}
    <div class="mobile-sheet-drag-area" id="mobileSheetDragArea">
        <div class="mobile-sheet-handle"></div>
    </div>

    {{-- Sheet Header --}}
    <div class="mobile-sheet-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('admin/assets/img/icons/brands/gudangku-.png') }}" alt="INVAS" style="height: 24px; max-width: 110px; object-fit: contain;">
            <span class="badge bg-label-primary font-monospace small" style="font-size: 0.65rem;">NAVIGASI</span>
        </div>
        <button type="button" class="btn p-0 text-muted" onclick="closeMobileMenuSheet()" aria-label="Tutup menu" style="border: none; background: transparent;">
            <i class="bx bx-x fs-3"></i>
        </button>
    </div>

    {{-- Sheet Content (Scrollable Grid with Categories) --}}
    <div class="mobile-sheet-body" id="mobileSheetBody">
        
        {{-- Section 1: INVENTORY --}}
        <div class="d-flex align-items-center gap-2 mb-2 mt-1">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">INVENTORY</small>
            <hr class="flex-grow-1 my-0 opacity-25">
        </div>
        <div class="row g-2 mb-3">
            {{-- Data Barang --}}
            <div class="col-4">
                <a href="{{ route('barang.index') }}" class="app-tile py-2 {{ Request::is('admin/barang*') && !Request::is('admin/barang-export*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-package"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Master Barang</div>
                </a>
            </div>

            {{-- Unit Serial Number --}}
            <div class="col-4">
                <a href="{{ route('inventory-item.index') }}" class="app-tile py-2 {{ Request::is('admin/inventory-item*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-barcode"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Unit Serial</div>
                </a>
            </div>

            {{-- Barang Masuk --}}
            <div class="col-4">
                <a href="{{ route('brg-masuk.index') }}" class="app-tile py-2 {{ Request::is('admin/brg-masuk*') && !Request::is('admin/brg-masuk-export*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-import"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Barang Masuk</div>
                </a>
            </div>

            {{-- Barang Keluar --}}
            <div class="col-4">
                <a href="{{ route('brg-keluar.index') }}" class="app-tile py-2 {{ Request::is('admin/brg-keluar*') && !Request::is('admin/brg-keluar-export*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Barang Keluar</div>
                </a>
            </div>

            {{-- Buku Mutasi / Audit --}}
            <div class="col-4">
                <a href="{{ route('stock-movement.index') }}" class="app-tile py-2 {{ Request::is('admin/stock-movements*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-history"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Buku Mutasi</div>
                </a>
            </div>

            {{-- Koreksi Stok --}}
            <div class="col-4">
                <a href="{{ route('stock-adjustment.index') }}" class="app-tile py-2 {{ Request::is('admin/stock-adjustments*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-slider-alt"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Koreksi Stok</div>
                </a>
            </div>
        </div>

        {{-- Section 2: TRANSAKSI --}}
        <div class="d-flex align-items-center gap-2 mb-2">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">TRANSAKSI</small>
            <hr class="flex-grow-1 my-0 opacity-25">
        </div>
        <div class="row g-2 mb-3">
            {{-- Pinjam Barang --}}
            <div class="col-4">
                <a href="{{ route('peminjaman.index') }}" class="app-tile py-2 {{ Request::is('admin/peminjaman*') && !Request::is('admin/peminjaman-export*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-calendar-event"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Peminjaman</div>
                </a>
            </div>

            {{-- Pengembalian --}}
            <div class="col-4">
                <a href="{{ route('pengembalian.index') }}" class="app-tile py-2 {{ Request::is('admin/pengembalian*') && !Request::is('admin/pengembalian-export*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-undo"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Pengembalian</div>
                </a>
            </div>

            {{-- Transfer Stok Ruangan --}}
            <div class="col-4">
                <a href="{{ route('brg-ruangan.index') }}" class="app-tile py-2 {{ Request::is('admin/brg-ruangan*') && !Request::is('admin/brg-ruangan-export*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-transfer-alt"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Transfer Stok</div>
                </a>
            </div>
        </div>

        {{-- Section 3: MASTER DATA, LAPORAN & SISTEM --}}
        <div class="d-flex align-items-center gap-2 mb-2">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">MASTER DATA & LAPORAN</small>
            <hr class="flex-grow-1 my-0 opacity-25">
        </div>
        <div class="row g-2 mb-3">
            {{-- Pusat Laporan --}}
            <div class="col-4">
                <a href="{{ route('reports.index') }}" class="app-tile py-2 {{ Request::is('admin/reports*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-file-blank"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Pusat Laporan</div>
                </a>
            </div>

            {{-- Statistik --}}
            <div class="col-4">
                <a href="{{ route('admin.statistik') }}" class="app-tile py-2 {{ Request::is('admin/statistik*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-pie-chart-alt-2"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Statistik</div>
                </a>
            </div>

            {{-- Data Ruangan (Admin) --}}
            @if (Auth::user()->is_admin == 1)
                <div class="col-4">
                    <a href="{{ route('ruangan.index') }}" class="app-tile py-2 {{ Request::is('admin/ruangan*') && !Request::is('admin/ruangan-export*') ? 'border-primary' : '' }}">
                        <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                            <i class="bx bx-map-pin"></i>
                        </div>
                        <div class="app-tile-label" style="font-size: 0.78rem;">Data Ruangan</div>
                    </a>
                </div>
            @endif

            {{-- Data Vendor --}}
            <div class="col-4">
                <a href="{{ route('vendor.index') }}" class="app-tile py-2 {{ Request::is('admin/vendor*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-store"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Data Vendor</div>
                </a>
            </div>

            {{-- Data Petugas (Admin) --}}
            @if (Auth::user()->is_admin == 1)
                <div class="col-4">
                    <a href="{{ route('karyawan.index') }}" class="app-tile py-2 {{ Request::is('admin/karyawan*') && !Request::is('admin/karyawan-export*') ? 'border-primary' : '' }}">
                        <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                            <i class="bx bx-user-check"></i>
                        </div>
                        <div class="app-tile-label" style="font-size: 0.78rem;">Data Petugas</div>
                    </a>
                </div>
            @endif

            {{-- Profil --}}
            <div class="col-4">
                <a href="javascript:void(0)" onclick="closeMobileMenuSheet(); showProfileTodo();" class="app-tile py-2">
                    <div class="app-tile-icon mb-1" style="width: 38px; height: 38px; font-size: 1.25rem;">
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="app-tile-label" style="font-size: 0.78rem;">Profil Akun</div>
                </a>
            </div>
        </div>

        {{-- Logout Button in Sheet --}}
        <div class="pt-2 border-top">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form-sheet').submit();" 
               class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-3 small fw-semibold">
                <i class="bx bx-power-off"></i>
                <span>Keluar Aplikasi</span>
            </a>
            <form id="logout-form-sheet" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
