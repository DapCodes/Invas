@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('admin/assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, viewport-fit=cover" />

    <title>INVAS - Sistem Inventaris SMK Assalaam</title>
    <meta name="description" content="Sistem Informasi Manajemen Logistik dan Inventaris Sekolah SMK Assalaam" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/img/favicon/gudangku-icon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Custom Minimalist App Launcher Styles -->
    <style>
        :root {
            --invas-primary: #2563eb;
            --invas-primary-hover: #1d4ed8;
            --invas-primary-light: #eff6ff;
            --invas-primary-subtle: #dbeafe;
            --invas-bg: #f8fafc;
            --invas-card-bg: #ffffff;
            --invas-border: #e2e8f0;
            --invas-text: #0f172a;
            --invas-text-muted: #64748b;
            --invas-radius: 16px;
            --invas-sidebar-w: 235px;
            --invas-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            font-family: var(--invas-font) !important;
            background-color: var(--invas-bg) !important;
            color: var(--invas-text);
            -webkit-tap-highlight-color: transparent;
        }

        /* ----------------------------------------------------
           SLIM DESKTOP SIDEBAR
           ---------------------------------------------------- */
        @media (min-width: 992px) {
            .layout-menu-fixed .layout-menu,
            .layout-menu {
                width: var(--invas-sidebar-w) !important;
            }

            .layout-page {
                padding-left: var(--invas-sidebar-w) !important;
            }
        }

        .slim-sidebar {
            background: #ffffff !important;
            border-right: 1px solid var(--invas-border) !important;
            box-shadow: none !important;
            height: 100vh !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            z-index: 1000 !important;
        }

        .slim-sidebar .app-brand {
            padding: 1.15rem 1.25rem;
            height: auto;
            flex-shrink: 0;
        }

        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.25rem 0.65rem;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 99px;
        }

        .slim-sidebar .menu-header-text {
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.6px !important;
            color: #94a3b8 !important;
            text-transform: uppercase;
            padding: 0 0.5rem;
        }

        .slim-sidebar .menu-inner {
            padding: 0 !important;
        }

        .slim-sidebar .menu-item {
            margin-bottom: 2px;
            padding: 0 !important;
        }

        .slim-sidebar .menu-item .menu-link {
            border-radius: 8px !important;
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--invas-text-muted);
            padding: 0.5rem 0.75rem !important;
            transition: all 0.18s ease;
            margin: 0 !important;
        }

        .slim-sidebar .menu-item .menu-link:hover {
            background-color: #f1f5f9 !important;
            color: var(--invas-text) !important;
        }

        /* Fix active state overflow effect */
        .slim-sidebar .menu-item.active > .menu-link::before,
        .slim-sidebar .menu-item.active > .menu-link::after,
        .slim-sidebar .menu-vertical .menu-item.active > .menu-link::after {
            display: none !important;
            content: none !important;
        }

        .slim-sidebar .menu-item.active .menu-link {
            background: var(--invas-primary-light) !important;
            color: var(--invas-primary) !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            box-shadow: none !important;
        }

        .sidebar-footer {
            flex-shrink: 0;
            background: #ffffff;
            border-top: 1px solid var(--invas-border);
        }

        .sidebar-user-link {
            transition: background-color 0.18s ease;
        }

        .sidebar-user-link:hover {
            background-color: #f1f5f9;
        }

        /* ----------------------------------------------------
           CLEAN TOPBAR
           ---------------------------------------------------- */
        .minimal-navbar {
            background: rgba(255, 255, 255, 0.92) !important;
            backdrop-filter: blur(10px) !important;
            border-bottom: 1px solid var(--invas-border) !important;
            box-shadow: none !important;
            padding: 0.65rem 1.25rem !important;
        }

        /* Table Responsive & Action Column */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th:last-child,
        .table td:last-child,
        .table th.action-col,
        .table td.action-col {
            white-space: nowrap !important;
            text-align: center !important;
            width: 1% !important;
            min-width: 90px !important;
        }

        .table .dropdown-menu {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            z-index: 1080 !important;
            border: 1px solid var(--invas-border) !important;
        }

        /* ----------------------------------------------------
           APP LAUNCHER TILE STYLES (1 MENU = 1 KOTAK)
           ---------------------------------------------------- */
        .app-tile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--invas-radius);
            padding: 1.25rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            color: var(--invas-text);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            user-select: none;
            height: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .app-tile:hover {
            transform: translateY(-3px);
            border-color: #cbd5e1;
            box-shadow: 0 10px 22px -5px rgba(15, 23, 42, 0.06);
            color: var(--invas-primary);
        }

        .app-tile:active {
            transform: scale(0.97);
        }

        .app-tile-icon {
            width: 44px;
            height: 44px;
            background: var(--invas-primary-light);
            color: var(--invas-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .app-tile:hover .app-tile-icon {
            background: var(--invas-primary);
            color: #ffffff;
        }

        .app-tile-label {
            font-size: 0.88rem;
            font-weight: 600;
            line-height: 1.25;
            color: var(--invas-text);
            margin-bottom: 2px;
        }

        .app-tile:hover .app-tile-label {
            color: var(--invas-primary);
        }

        /* ----------------------------------------------------
           MOBILE BOTTOM NAVIGATION (3 MENU)
           ---------------------------------------------------- */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-top: 1px solid var(--invas-border);
            padding: 6px 16px;
            padding-bottom: calc(6px + env(safe-area-inset-bottom, 0px));
        }

        .mobile-bottom-nav-container {
            display: flex;
            align-items: center;
            justify-content: space-around;
            max-width: 420px;
            margin: 0 auto;
            position: relative;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--invas-text-muted);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .mobile-nav-item i {
            font-size: 1.35rem;
            line-height: 1;
            margin-bottom: 2px;
        }

        .mobile-nav-item.active,
        .mobile-nav-item:hover {
            color: var(--invas-primary);
        }

        .mobile-nav-home {
            position: relative;
            top: -10px;
            padding: 0 4px;
        }

        .mobile-home-btn {
            width: 48px;
            height: 48px;
            background: var(--invas-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.45rem;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
            border: 3px solid #ffffff;
            transition: transform 0.2s ease;
        }

        .mobile-nav-home:active .mobile-home-btn {
            transform: scale(0.92);
        }

        /* ----------------------------------------------------
           DRAGGABLE BOTTOM SHEET MODAL
           ---------------------------------------------------- */
        .mobile-sheet-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(3px);
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s;
        }

        .mobile-sheet-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-sheet-modal {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1060;
            background: #ffffff;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.12);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
            touch-action: pan-y;
        }

        .mobile-sheet-modal.active {
            transform: translateY(0);
        }

        .mobile-sheet-drag-area {
            width: 100%;
            padding: 10px 0 4px 0;
            display: flex;
            justify-content: center;
            cursor: grab;
            user-select: none;
        }

        .mobile-sheet-handle {
            width: 38px;
            height: 4px;
            background-color: #cbd5e1;
            border-radius: 99px;
        }

        .mobile-sheet-header {
            padding: 6px 16px 10px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .mobile-sheet-body {
            padding: 14px 16px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: calc(24px + env(safe-area-inset-bottom, 0px));
        }

        /* Adjust layout spacing on mobile */
        @media (max-width: 991.98px) {
            .layout-page {
                padding-bottom: 78px !important;
            }

            .container-xxl, .container {
                padding-left: 0.85rem !important;
                padding-right: 0.85rem !important;
            }
        }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('admin/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- SLIM DESKTOP SIDEBAR -->
            @include('components.sidebar')
            <!-- / SLIM DESKTOP SIDEBAR -->

            <div class="layout-page">
                <!-- Top Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center minimal-navbar"
                    id="layout-navbar">

                    <!-- Mobile Left Brand -->
                    <div class="d-flex align-items-center d-lg-none me-auto">
                        <a href="{{ route('admin.home') }}" class="d-flex align-items-center text-decoration-none">
                            <img src="{{ asset('admin/assets/img/icons/brands/gudangku-.png') }}" alt="INVAS" style="height: 26px; max-width: 120px; object-fit: contain;">
                        </a>
                    </div>

                    <!-- Desktop Page Title -->
                    <div class="navbar-nav align-items-center d-none d-lg-flex">
                        <h6 class="mb-0 fw-bold text-dark">@yield('page-title', 'Beranda')</h6>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center ms-auto" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
                            <!-- User Dropdown -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center gap-2" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="d-none d-md-flex flex-column text-end">
                                        <span class="fw-semibold text-dark fs-7 lh-sm">{{ Auth::user()->name }}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ Auth::user()->is_admin ? 'Administrator' : 'Petugas' }}</small>
                                    </div>
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('admin/assets/img/avatars/2.png') }}" alt="Avatar"
                                            class="w-px-36 h-auto rounded-circle border" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 p-1 mt-2">
                                    <li class="p-2 border-bottom mb-1">
                                        <span class="fw-bold d-block text-dark small">{{ Auth::user()->name }}</span>
                                        <span class="badge bg-label-primary text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->is_admin ? 'Admin' : 'Petugas' }}</span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-2 py-1" href="javascript:void(0)" onclick="showProfileTodo()">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle fw-medium">Profil Pengguna</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-2 text-danger py-1" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle fw-medium">Keluar</span>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Top Navbar -->

                <!-- Main Content -->
                <div class="container-xxl flex-grow-1 container-p-y mt-2">
                    @yield('content')
                </div>
                <!-- / Main Content -->

            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Mobile Navigation Components (Mobile Only) -->
    @include('components.bottom-nav')
    @include('components.mobile-sheet')

    <!-- Core JS -->
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Profile ToDo Dialog -->
    <script>
        function showProfileTodo() {
            Swal.fire({
                icon: 'info',
                title: 'Profil Pengguna',
                text: 'Halaman profil dan pengaturan akun akan segera hadir!',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Mengerti'
            });
        }
    </script>

    <!-- Draggable Bottom Sheet Script -->
    <script>
        function openMobileMenuSheet() {
            const sheet = document.getElementById('mobileMenuSheet');
            const backdrop = document.getElementById('mobileSheetBackdrop');
            if (sheet && backdrop) {
                backdrop.classList.add('active');
                sheet.classList.add('active');
                sheet.style.transform = '';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileMenuSheet() {
            const sheet = document.getElementById('mobileMenuSheet');
            const backdrop = document.getElementById('mobileSheetBackdrop');
            if (sheet && backdrop) {
                sheet.style.transform = '';
                sheet.classList.remove('active');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Touch Drag Gesture for Mobile Bottom Sheet
        document.addEventListener('DOMContentLoaded', function () {
            const sheet = document.getElementById('mobileMenuSheet');
            const dragArea = document.getElementById('mobileSheetDragArea');
            if (!sheet || !dragArea) return;

            let startY = 0;
            let currentY = 0;
            let isDragging = false;

            function onTouchStart(e) {
                startY = e.touches[0].clientY;
                isDragging = true;
                sheet.style.transition = 'none';
            }

            function onTouchMove(e) {
                if (!isDragging) return;
                currentY = e.touches[0].clientY;
                const deltaY = currentY - startY;

                if (deltaY > 0) {
                    sheet.style.transform = `translateY(${deltaY}px)`;
                }
            }

            function onTouchEnd() {
                if (!isDragging) return;
                isDragging = false;
                sheet.style.transition = 'transform 0.3s cubic-bezier(0.32, 0.72, 0, 1)';

                const deltaY = currentY - startY;
                if (deltaY > 80) {
                    closeMobileMenuSheet();
                } else {
                    sheet.style.transform = 'translateY(0)';
                }
            }

            dragArea.addEventListener('touchstart', onTouchStart, { passive: true });
            dragArea.addEventListener('touchmove', onTouchMove, { passive: true });
            dragArea.addEventListener('touchend', onTouchEnd);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMobileMenuSheet();
                }
            });
        });
    </script>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + id).submit();
                }
            });
        }
    </script>

    <script>
        function pilihBarang(id, nama, merek) {
            const input = document.getElementById('id_barang');
            const dropdown = document.getElementById('dropdownBarang');
            if (input) input.value = id;
            if (dropdown) {
                dropdown.innerHTML = `<i style="position: relative; right: 8px; bottom: 2px;" class="bx bx-box"></i> ${nama} - ${merek}`;
            }
        }
    </script>
</body>

</html>
