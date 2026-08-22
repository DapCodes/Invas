{{-- Mobile Bottom Navigation (5 Action Slots: Pinjam, Kembali, Home, Laporan, Menu) --}}
<nav class="mobile-bottom-nav d-lg-none" id="mobileBottomNav" aria-label="Mobile Navigation">
    <div class="mobile-bottom-nav-container">
        {{-- 1. Pinjam Barang --}}
        <a href="{{ route('peminjaman.index') }}" 
           class="mobile-nav-item {{ Request::is('admin/peminjaman*') ? 'active' : '' }}" 
           title="Pinjam Barang">
            <i class="bx bx-upload"></i>
            <span>Pinjam</span>
        </a>

        {{-- 2. Pengembalian --}}
        <a href="{{ route('pengembalian.index') }}" 
           class="mobile-nav-item {{ Request::is('admin/pengembalian*') ? 'active' : '' }}" 
           title="Pengembalian">
            <i class="bx bx-undo"></i>
            <span>Kembali</span>
        </a>

        {{-- 3. Home (Center Elevated) --}}
        <a href="{{ route('admin.home') }}" 
           class="mobile-nav-item mobile-nav-home {{ Request::is('admin/home*') ? 'active' : '' }}" 
           title="Beranda Utama">
            <div class="mobile-home-btn">
                <i class="bx bx-home-alt-2"></i>
            </div>
            <span>Home</span>
        </a>

        {{-- 4. Pusat Laporan --}}
        <a href="{{ route('reports.index') }}" 
           class="mobile-nav-item {{ Request::is('admin/reports*') ? 'active' : '' }}" 
           title="Pusat Laporan">
            <i class="bx bx-file-blank"></i>
            <span>Laporan</span>
        </a>

        {{-- 5. Semua Menu (Draggable Bottom Sheet Trigger) --}}
        <button type="button" 
                class="mobile-nav-item" 
                id="btnOpenMobileMenu" 
                onclick="openMobileMenuSheet()" 
                title="Semua Menu">
            <i class="bx bx-grid-alt"></i>
            <span>Menu</span>
        </button>
    </div>
</nav>
