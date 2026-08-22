{{-- Mobile Bottom Navigation (Tepat 3 Menu) --}}
<nav class="mobile-bottom-nav d-lg-none" id="mobileBottomNav" aria-label="Mobile Navigation">
    <div class="mobile-bottom-nav-container">
        {{-- 1. Pinjam Barang --}}
        <a href="{{ route('peminjaman.index') }}" 
           class="mobile-nav-item {{ Request::is('admin/peminjaman*') ? 'active' : '' }}" 
           title="Pinjam Barang">
            <i class="bx bx-upload"></i>
            <span>Pinjam</span>
        </a>

        {{-- 2. Home (Center Primary) --}}
        <a href="{{ route('admin.home') }}" 
           class="mobile-nav-item mobile-nav-home {{ Request::is('admin/home*') ? 'active' : '' }}" 
           title="Beranda Utama">
            <div class="mobile-home-btn">
                <i class="bx bx-home-alt-2"></i>
            </div>
            <span>Home</span>
        </a>

        {{-- 3. Menu (Draggable Bottom Sheet Trigger) --}}
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
