<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo mb-2 mt-2" style="position: relative; right: 10px;">
        <a href="{{ route('admin.home') }}" class="app-brand-link">
            <img style="width: 200px" src="{{ asset('admin/assets/img/icons/brands/gudangku-icon.png') }}" alt="">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1 gap-1">
        <!-- Dashboard -->
        <li class="menu-item {{ Request::is('admin/home*') ? 'active' : '' }}">
            <a href="{{ route('admin.home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div>Beranda</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('admin/statistik*') ? 'active' : '' }}">
            <a href="{{ route('admin.statistik') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                <div>Statistik</div>
            </a>
        </li>

        <!-- Header -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Mengelola</span>
        </li>

        <!-- Data Petugas -->
        @if (Auth::user()->is_admin == 1)
            <li class="menu-item {{ Request::is('admin/karyawan*') ? 'active' : '' }}">
                <a href="{{ route('karyawan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-id-card"></i>
                    <div>Data Petugas</div>
                </a>
            </li>
        @endif

        <!-- Data Barang -->
        <li class="menu-item {{ Request::is('admin/barang*') ? 'active' : '' }}">
            <a href="{{ route('barang.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Data Barang</div>
            </a>
        </li>

        <!-- Data Vendor -->
        <li class="menu-item {{ Request::is('admin/vendor*') ? 'active' : '' }}">
            <a href="{{ route('vendor.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>Data Vendor</div>
            </a>
        </li>

        <!-- Data Ruangan -->
        @if (Auth::user()->is_admin == 1)
            <li class="menu-item {{ Request::is('admin/ruangan*') ? 'active' : '' }}">
                <a href="{{ route('ruangan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-building-house"></i>
                    <div>Data Ruangan</div>
                </a>
            </li>
        @endif

        <!-- Data Barang Ruangan -->
        <li class="menu-item {{ Request::is('admin/brg-ruangan*') ? 'active' : '' }}">
            <a href="{{ route('brg-ruangan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div>Data Barang Ruangan</div>
            </a>
        </li>

        <!-- Barang Masuk -->
        <li class="menu-item {{ Request::is('admin/brg-masuk*') ? 'active' : '' }}">
            <a href="{{ route('brg-masuk.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-in-circle"></i>
                <div>Barang Masuk</div>
            </a>
        </li>

        <!-- Barang Keluar -->
        <li class="menu-item {{ Request::is('admin/brg-keluar*') ? 'active' : '' }}">
            <a href="{{ route('brg-keluar.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
                <div>Barang Keluar</div>
            </a>
        </li>

        <!-- Peminjaman -->
        <li class="menu-item {{ Request::is('admin/peminjaman*') ? 'active' : '' }}">
            <a href="{{ route('peminjaman.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-upload"></i>
                <div>Data Peminjaman</div>
            </a>
        </li>

        <!-- Pengembalian -->
        <li class="menu-item {{ Request::is('admin/pengembalian*') ? 'active' : '' }}">
            <a href="{{ route('pengembalian.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div>Data Pengembalian</div>
            </a>
        </li>
    </ul>
</aside>
