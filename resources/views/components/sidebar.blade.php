<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme d-none d-lg-flex slim-sidebar">
    {{-- Brand Header --}}
    <div class="app-brand demo py-3 px-3 mb-1 border-bottom">
        <a href="{{ route('admin.home') }}" class="d-flex align-items-center text-decoration-none py-1">
            <img src="{{ asset('admin/assets/img/icons/brands/gudangku-.png') }}" alt="INVAS" style="height: 30px; max-width: 140px; object-fit: contain;">
        </a>
    </div>

    {{-- Scrollable Menu Area --}}
    <div class="sidebar-nav-scroll">
        <ul class="menu-inner py-2">
            {{-- Section: UTAMA --}}
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Utama</span>
            </li>
            <li class="menu-item {{ Request::is('admin/home*') ? 'active' : '' }}">
                <a href="{{ route('admin.home') }}" class="menu-link">
                    <div>Beranda</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('admin/statistik*') ? 'active' : '' }}">
                <a href="{{ route('admin.statistik') }}" class="menu-link">
                    <div>Statistik</div>
                </a>
            </li>

            {{-- Section: TRANSAKSI --}}
            <li class="menu-header small text-uppercase mt-2">
                <span class="menu-header-text">Transaksi</span>
            </li>
            <li class="menu-item {{ Request::is('admin/peminjaman*') ? 'active' : '' }}">
                <a href="{{ route('peminjaman.index') }}" class="menu-link">
                    <div>Pinjam Barang</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('admin/pengembalian*') ? 'active' : '' }}">
                <a href="{{ route('pengembalian.index') }}" class="menu-link">
                    <div>Pengembalian</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('admin/brg-masuk*') ? 'active' : '' }}">
                <a href="{{ route('brg-masuk.index') }}" class="menu-link">
                    <div>Barang Masuk</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('admin/brg-keluar*') ? 'active' : '' }}">
                <a href="{{ route('brg-keluar.index') }}" class="menu-link">
                    <div>Barang Keluar</div>
                </a>
            </li>

            {{-- Section: MASTER DATA --}}
            <li class="menu-header small text-uppercase mt-2">
                <span class="menu-header-text">Master Data</span>
            </li>
            <li class="menu-item {{ Request::is('admin/barang*') ? 'active' : '' }}">
                <a href="{{ route('barang.index') }}" class="menu-link">
                    <div>Data Barang</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('admin/vendor*') ? 'active' : '' }}">
                <a href="{{ route('vendor.index') }}" class="menu-link">
                    <div>Data Vendor</div>
                </a>
            </li>
            @if (Auth::user()->is_admin == 1)
                <li class="menu-item {{ Request::is('admin/ruangan*') ? 'active' : '' }}">
                    <a href="{{ route('ruangan.index') }}" class="menu-link">
                        <div>Data Ruangan</div>
                    </a>
                </li>
            @endif
            <li class="menu-item {{ Request::is('admin/brg-ruangan*') ? 'active' : '' }}">
                <a href="{{ route('brg-ruangan.index') }}" class="menu-link">
                    <div>Barang Ruangan</div>
                </a>
            </li>
            @if (Auth::user()->is_admin == 1)
                <li class="menu-item {{ Request::is('admin/karyawan*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.index') }}" class="menu-link">
                        <div>Data Petugas</div>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    {{-- Bottom Profile & Logout Footer --}}
    <div class="sidebar-footer p-3 border-top mt-auto">
        <a href="javascript:void(0)" onclick="showProfileTodo()" class="d-flex align-items-center gap-2 text-decoration-none text-dark mb-2 p-1 rounded sidebar-user-link">
            <div class="avatar avatar-online avatar-sm">
                <img src="{{ asset('admin/assets/img/avatars/2.png') }}" alt="Avatar" class="w-px-32 h-auto rounded-circle border" />
            </div>
            <div class="d-flex flex-column text-truncate" style="line-height: 1.2;">
                <span class="fw-semibold small text-truncate">{{ Auth::user()->name }}</span>
                <span class="text-muted" style="font-size: 0.68rem;">{{ Auth::user()->is_admin ? 'Administrator' : 'Petugas' }}</span>
            </div>
        </a>
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" 
           class="btn btn-sm btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-1 rounded-2" 
           style="font-size: 0.75rem;">
            <i class="bx bx-power-off"></i>
            <span>Keluar</span>
        </a>
        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>
