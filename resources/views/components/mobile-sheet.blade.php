{{-- Mobile Draggable Bottom Sheet Modal (App Launcher Style) --}}
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
        </div>
        <button type="button" class="btn p-0 text-muted" onclick="closeMobileMenuSheet()" aria-label="Tutup menu" style="border: none; background: transparent;">
            <i class="bx bx-x fs-3"></i>
        </button>
    </div>

    {{-- Sheet Content (Scrollable Grid) --}}
    <div class="mobile-sheet-body" id="mobileSheetBody">
        {{-- App Launcher Grid (1 Menu = 1 Card) --}}
        <div class="row g-2 mb-3">
            {{-- 1. Pinjam Barang (Prioritas) --}}
            <div class="col-6">
                <a href="{{ route('peminjaman.index') }}" class="app-tile py-3 {{ Request::is('admin/peminjaman*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-upload"></i>
                    </div>
                    <div class="app-tile-label">Pinjam Barang</div>
                </a>
            </div>

            {{-- 2. Data Barang --}}
            <div class="col-6">
                <a href="{{ route('barang.index') }}" class="app-tile py-3 {{ Request::is('admin/barang*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-package"></i>
                    </div>
                    <div class="app-tile-label">Data Barang</div>
                </a>
            </div>

            {{-- 3. Barang Masuk --}}
            <div class="col-6">
                <a href="{{ route('brg-masuk.index') }}" class="app-tile py-3 {{ Request::is('admin/brg-masuk*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-log-in-circle"></i>
                    </div>
                    <div class="app-tile-label">Barang Masuk</div>
                </a>
            </div>

            {{-- 4. Barang Keluar --}}
            <div class="col-6">
                <a href="{{ route('brg-keluar.index') }}" class="app-tile py-3 {{ Request::is('admin/brg-keluar*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-log-out-circle"></i>
                    </div>
                    <div class="app-tile-label">Barang Keluar</div>
                </a>
            </div>

            {{-- 5. Pengembalian --}}
            <div class="col-6">
                <a href="{{ route('pengembalian.index') }}" class="app-tile py-3 {{ Request::is('admin/pengembalian*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-history"></i>
                    </div>
                    <div class="app-tile-label">Pengembalian</div>
                </a>
            </div>

            {{-- 6. Barang Ruangan --}}
            <div class="col-6">
                <a href="{{ route('brg-ruangan.index') }}" class="app-tile py-3 {{ Request::is('admin/brg-ruangan*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-box"></i>
                    </div>
                    <div class="app-tile-label">Barang Ruangan</div>
                </a>
            </div>

            {{-- 7. Data Vendor --}}
            <div class="col-6">
                <a href="{{ route('vendor.index') }}" class="app-tile py-3 {{ Request::is('admin/vendor*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-store"></i>
                    </div>
                    <div class="app-tile-label">Data Vendor</div>
                </a>
            </div>

            {{-- 8. Data Ruangan (Admin) --}}
            @if (Auth::user()->is_admin == 1)
                <div class="col-6">
                    <a href="{{ route('ruangan.index') }}" class="app-tile py-3 {{ Request::is('admin/ruangan*') ? 'border-primary' : '' }}">
                        <div class="app-tile-icon mb-2">
                            <i class="bx bx-building-house"></i>
                        </div>
                        <div class="app-tile-label">Data Ruangan</div>
                    </a>
                </div>
            @endif

            {{-- 9. Data Petugas (Admin) --}}
            @if (Auth::user()->is_admin == 1)
                <div class="col-6">
                    <a href="{{ route('karyawan.index') }}" class="app-tile py-3 {{ Request::is('admin/karyawan*') ? 'border-primary' : '' }}">
                        <div class="app-tile-icon mb-2">
                            <i class="bx bx-id-card"></i>
                        </div>
                        <div class="app-tile-label">Data Petugas</div>
                    </a>
                </div>
            @endif

            {{-- 10. Statistik --}}
            <div class="col-6">
                <a href="{{ route('admin.statistik') }}" class="app-tile py-3 {{ Request::is('admin/statistik*') ? 'border-primary' : '' }}">
                    <div class="app-tile-icon mb-2">
                        <i class="bx bx-line-chart"></i>
                    </div>
                    <div class="app-tile-label">Statistik</div>
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
