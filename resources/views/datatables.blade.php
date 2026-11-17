<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Starter Page</title>

    <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
    <script>
        (() => {
            'use strict';
            const root = document.documentElement;

            // Applications with their own theming opt out of AdminLTE's color mode
            // entirely, here as well as in the bundle.
            if (root.getAttribute('data-lte-color-mode') === 'off') {
                return;
            }

            const STORAGE_KEY = 'lte-theme';
            let stored = null;
            try {
                stored = localStorage.getItem(STORAGE_KEY);
            } catch {
                // localStorage may be unavailable (private mode, sandboxed iframe).
            }
            // Mirror the precedence in color-mode.ts: the visitor's stored choice
            // wins, then a theme this page declared itself, then the OS preference.
            const authored = root.getAttribute('data-bs-theme');
            let resolved = 'light';
            if (stored === 'dark' || stored === 'light') {
                resolved = stored;
            } else if (authored === 'dark' || authored === 'light') {
                resolved = authored;
            } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
                resolved = 'dark';
            }
            root.setAttribute('data-bs-theme', resolved);
            root.style.colorScheme = resolved;
            // Flag values computed here, so the bundle does not mistake them for a
            // theme the page declared and stop following the OS preference.
            if (resolved !== authored) {
                root.setAttribute('data-lte-theme-resolved', '');
            }
        })();
    </script>
    <!--end::Theme Init-->

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Starter Page" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a free Bootstrap 5 admin dashboard template with almost 50 example pages, built with vanilla JS and designed with accessibility in mind." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel" />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="{{ asset('dist/css/adminlte.css') }}" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <!--begin::Required Plugin(Tabulator)-->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/tabulator-tables@6.4.0/dist/css/tabulator_bootstrap5.min.css"
        crossorigin="anonymous" />
    <!--end::Required Plugin(Tabulator)-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"
                            aria-label="Toggle sidebar">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->


                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <!--begin::Search (small screens: the field above is hidden, so link to the search page)-->
                    <li class="nav-item d-md-none">
                        <a class="nav-link" href="./pages/search-results.html" aria-label="Search">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </a>
                    </li>
                    <!--end::Search-->

                    <!--begin::Fullscreen Toggle-->
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="Toggle fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
                        </a>
                    </li>
                    <!--end::Fullscreen Toggle-->

                    <!--begin::Color Mode Toggle (#6010)-->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="bd-theme" aria-label="Toggle color scheme"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
                            <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
                            <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme"
                            style="--bs-dropdown-min-width: 8rem">
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="light" aria-pressed="false">
                                    <i class="bi bi-sun-fill me-2"></i>
                                    Light
                                    <i class="bi bi-check-lg ms-auto d-none"></i>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center"
                                    data-bs-theme-value="dark" aria-pressed="false">
                                    <i class="bi bi-moon-fill me-2"></i>
                                    Dark
                                    <i class="bi bi-check-lg ms-auto d-none"></i>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center active"
                                    data-bs-theme-value="auto" aria-pressed="true">
                                    <i class="bi bi-circle-half me-2"></i>
                                    Auto
                                    <i class="bi bi-check-lg ms-auto d-none"></i>
                                </button>
                            </li>
                        </ul>
                    </li>
                    <!--end::Color Mode Toggle-->

                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="{{ asset('dist/assets/img/user2-160x160.jpg') }}"
                                class="user-image rounded-circle shadow" alt="Alexander Pierce" />
                            <span class="d-none d-md-inline">Alexander Pierce</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-primary">
                                <img src="{{ asset('dist/assets/img/user2-160x160.jpg') }}"
                                    class="rounded-circle shadow" alt="Alexander Pierce" />
                                <p>
                                    Alexander Pierce - Web Developer
                                    <small>Member since Nov. 2023</small>
                                </p>
                            </li>
                            <!--end::User Image-->
                            <!--begin::Menu Body-->
                            <li class="user-body">
                                <!--begin::Row-->
                                <div class="row">
                                    <div class="col-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </div>
                                <!--end::Row-->
                            </li>
                            <!--end::Menu Body-->
                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                <a href="#" class="btn btn-outline-secondary">Profile</a>
                                <a href="#" class="btn btn-outline-danger float-end">Sign out</a>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="./index.html" class="brand-link">
                    <!--begin::Brand Image-->
                    <img src="{{ asset('dist/assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                        class="brand-image opacity-75 shadow" />
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">AdminLTE 4</span>
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->
            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2" aria-label="Main navigation">
                    <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false"
                        id="navigation">
                        <li class="nav-item">
                            <a href="/" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/tables" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>
                                    Tables
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/datatables" class="nav-link active">
                                <i class="nav-icon bi bi-table"></i>
                                <p>
                                    DataTables
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>
                                    Data Master
                                    <span class="nav-badge badge text-bg-secondary me-3">12</span>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Master 1</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Master 2</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <!--end::Sidebar Menu-->

                    <!-- Docs CTA (bottom of sidebar) -->
                    <div class="p-3 mt-3 border-top border-secondary border-opacity-25">
                        <a href="./docs/introduction.html"
                            class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-book" aria-hidden="true"></i>
                            View documentation
                        </a>
                    </div>
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 class="mb-0 fs-3">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!-- Data Table -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-4">
                                    <h3 class="card-title">User Directory</h3>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#modal-add-user">
                                            <i class="bi bi-plus-circle me-1" aria-hidden="true"> </i>
                                            New user
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-3">

                            </div>
                            <div id="users-table"></div>
                        </div>
                        <div class="card-footer text-secondary small">
                            Powered by
                            <a href="https://tabulator.info/" target="_blank" rel="noopener">Tabulator</a>
                            &mdash; vanilla JS, no jQuery required.
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->

            <!--begin::Add User Modal-->
            <div class="modal fade" id="modal-add-user" tabindex="-1" aria-labelledby="modal-add-user-label"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-add-user-label">Add new user</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="new-user-name" class="form-label"> Full name </label>
                                    <input type="text" class="form-control" id="new-user-name"
                                        placeholder="e.g. Jane Doe" required />
                                </div>
                                <div class="mb-3">
                                    <label for="new-user-email" class="form-label"> Email address </label>
                                    <input type="email" class="form-control" id="new-user-email"
                                        placeholder="name@example.com" required />
                                    <div class="form-text">The invitation will be sent to this address.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="new-user-role" class="form-label"> Role </label>
                                    <select id="new-user-role" class="form-select">
                                        <option selected>Subscriber</option>
                                        <option>Author</option>
                                        <option>Editor</option>
                                        <option>Administrator</option>
                                    </select>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="new-user-welcome" checked />
                                    <label class="form-check-label" for="new-user-welcome">
                                        Send a welcome email with login details
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Create user</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end::Add User Modal-->


            <!--begin::Delete User Modal-->
            <div class="modal fade" id="modal-delete-user" tabindex="-1" aria-labelledby="modal-delete-user-label"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-delete-user-label">Delete user</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">
                                Yakin ingin menghapus <strong id="delete-user-name"></strong>?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger" id="confirm-delete">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Delete User Modal-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                Copyright &copy; 2014-2026&nbsp;
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>

    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

            // Disable OverlayScrollbars on mobile devices to prevent touch interference
            const isMobile = window.innerWidth <= 992;

            if (
                sidebarWrapper &&
                OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
                !isMobile
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!--begin::Color Mode Toggle-->
    <!-- The light/dark/auto switcher ships in adminlte.js as the ColorMode
     module (since 4.1) — no page script needed. Only the no-flash snippet
     in <head> stays inline, because it must run before first paint. -->
    <!--end::Color Mode Toggle-->

    <!-- begin DataTables -->
    <script src="https://cdn.jsdelivr.net/npm/tabulator-tables@6.4.0/dist/js/tabulator.min.js" crossorigin="anonymous">
    </script>
    <script>
        const statusBadge = (cell) => {
            const value = cell.getValue();
            const map = {
                Active: 'success',
                Invited: 'info',
                Suspended: 'secondary'
            };
            const color = map[value] || 'secondary';
            return `<span class="badge text-bg-${color}">${value}</span>`;
        };

        // Hindari HTML injection dari nama atau data user
        const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));

        const editBaseUrl = "{{ url('users') }}"; // sesuaikan dengan route edit Anda

        const actionButtons = (cell) => {
            const row = cell.getRow().getData();
            const name = escapeHtml(row.name);

            return `
        <div class="btn-group btn-group-sm">
            <a href="${editBaseUrl}/${row.id}/edit" class="btn btn-outline-secondary"
               aria-label="Edit ${name}">
                <i class="bi bi-pencil" aria-hidden="true"></i>
            </a>
            <button type="button" class="btn btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#modal-delete-user"
                    data-id="${row.id}" data-name="${name}"
                    aria-label="Hapus ${name}">
                <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
        </div>`;
        };

        document.addEventListener('DOMContentLoaded', () => {
            const data = [{
                    id: 1,
                    name: 'Olivia Bennett',
                    email: 'olivia@example.com',
                    role: 'Admin',
                    status: 'Active',
                    joined: '2024-03-12',
                },
                {
                    id: 2,
                    name: 'Liam Carter',
                    email: 'liam@example.com',
                    role: 'Editor',
                    status: 'Active',
                    joined: '2024-04-08',
                },
                {
                    id: 3,
                    name: 'Emma Dawson',
                    email: 'emma@example.com',
                    role: 'Viewer',
                    status: 'Invited',
                    joined: '2024-06-21',
                },
                {
                    id: 4,
                    name: 'Noah Evans',
                    email: 'noah@example.com',
                    role: 'Editor',
                    status: 'Suspended',
                    joined: '2024-07-15',
                },
                {
                    id: 5,
                    name: 'Ava Foster',
                    email: 'ava@example.com',
                    role: 'Admin',
                    status: 'Active',
                    joined: '2024-08-30',
                },
                {
                    id: 6,
                    name: 'Ethan Grant',
                    email: 'ethan@example.com',
                    role: 'Viewer',
                    status: 'Active',
                    joined: '2024-09-14',
                },
                {
                    id: 7,
                    name: 'Sophia Hayes',
                    email: 'sophia@example.com',
                    role: 'Editor',
                    status: 'Active',
                    joined: '2024-10-02',
                },
                {
                    id: 8,
                    name: 'Mason Ingram',
                    email: 'mason@example.com',
                    role: 'Viewer',
                    status: 'Invited',
                    joined: '2024-11-19',
                },
                {
                    id: 9,
                    name: 'Isabella Jones',
                    email: 'isabella@example.com',
                    role: 'Admin',
                    status: 'Active',
                    joined: '2025-01-05',
                },
                {
                    id: 10,
                    name: 'Lucas Klein',
                    email: 'lucas@example.com',
                    role: 'Viewer',
                    status: 'Suspended',
                    joined: '2025-02-18',
                },
                {
                    id: 11,
                    name: 'Mia Lopez',
                    email: 'mia@example.com',
                    role: 'Editor',
                    status: 'Active',
                    joined: '2025-03-22',
                },
                {
                    id: 12,
                    name: 'Logan Moore',
                    email: 'logan@example.com',
                    role: 'Viewer',
                    status: 'Active',
                    joined: '2025-04-09',
                },
                {
                    id: 13,
                    name: 'Charlotte Nelson',
                    email: 'charlotte@example.com',
                    role: 'Admin',
                    status: 'Active',
                    joined: '2025-04-27',
                },
                {
                    id: 14,
                    name: 'Henry Owens',
                    email: 'henry@example.com',
                    role: 'Editor',
                    status: 'Invited',
                    joined: '2025-05-11',
                },
                {
                    id: 15,
                    name: 'Amelia Price',
                    email: 'amelia@example.com',
                    role: 'Viewer',
                    status: 'Active',
                    joined: '2025-05-17',
                },
            ];

            const table = new Tabulator('#users-table', {
                data: data,
                layout: 'fitColumns',
                pagination: true,
                paginationSize: 10,
                paginationSizeSelector: [10, 25, 50, 100],
                movableColumns: true,
                columns: [{
                        title: '#',
                        field: 'id',
                        width: 60,
                        headerSort: true
                    },
                    {
                        title: 'Name',
                        field: 'name',
                        headerFilter: 'input'
                    },
                    {
                        title: 'Email',
                        field: 'email',
                        headerFilter: 'input'
                    },
                    {
                        title: 'Role',
                        field: 'role',
                        headerFilter: 'list',
                        headerFilterParams: {
                            values: ['', 'Admin', 'Editor', 'Viewer']
                        },
                        width: 120,
                    },
                    {
                        title: 'Status',
                        field: 'status',
                        formatter: statusBadge,
                        headerFilter: 'list',
                        headerFilterParams: {
                            values: ['', 'Active', 'Invited', 'Suspended']
                        },
                        width: 130,
                        hozAlign: 'center',
                    },
                    // {
                    //     title: 'Joined',
                    //     field: 'joined',
                    //     sorter: 'date',
                    //     width: 130
                    // },
                    {
                        title: 'Aksi',
                        field: 'actions',
                        formatter: actionButtons,
                        headerSort: false,
                        hozAlign: 'center',
                        width: 110,
                        download: false, // jangan ikut ke CSV/JSON
                        print: false, // jangan ikut saat print
                    },
                ],
            });

            const modalEl = document.getElementById('modal-delete-user');
            let deleteId = null;

            // Saat modal dibuka, ambil data dari tombol yang diklik
            modalEl.addEventListener('show.bs.modal', (event) => {
                const button = event.relatedTarget;
                deleteId = button.dataset.id;
                document.getElementById('delete-user-name').textContent = button.dataset.name;
            });

            document.getElementById('confirm-delete').addEventListener('click', () => {
                // Demo: hapus baris dari tabel
                table.deleteRow(deleteId);
                bootstrap.Modal.getInstance(modalEl).hide();

                // Untuk Laravel, submit form DELETE ke server, misalnya:
                // const form = document.getElementById('delete-form');
                // form.action = `{{ url('users') }}/${deleteId}`;
                // form.submit();
            });
        });
    </script>
    <!-- end DataTables -->
    <!--end::Script-->
</body>
<!--end::Body-->

</html>
