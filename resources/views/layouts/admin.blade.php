<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - LokalBites Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, #e91e63 0%, #ad1457 100%);
            --sidebar-width: 220px;
        }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            padding-top: 0;
        }
        .sidebar .brand {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        .sidebar .brand h4 {
            color: #fff;
            font-weight: 800;
            font-size: 1.3rem;
            margin: 0;
            letter-spacing: 1px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.12);
            border-left-color: #fff;
        }
        .sidebar .nav-link i { width: 24px; margin-right: 8px; text-align: center; }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 12px 30px;
            border-bottom: 1px solid #e3e6ea;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .topbar .user-name { font-weight: 600; color: #333; }
        .content-area { padding: 25px 30px; }
        .card { border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05); border-radius: 8px; }
        .card-header { background: #fff; border-bottom: 1px solid #e3e6ea; }
        .table th { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #666; }
        .btn-add { background: #1cc88a; border: none; color: #fff; font-weight: 600; }
        .btn-add:hover { background: #17a673; color: #fff; }
        .btn-edit { background: #36b9cc; border: none; color: #fff; font-size: 0.8rem; }
        .btn-edit:hover { background: #2c9faf; color: #fff; }
        .btn-delete { background: #e74a3b; border: none; color: #fff; font-size: 0.8rem; }
        .btn-delete:hover { background: #c0392b; color: #fff; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: #333; }
        .stat-card { border-left: 4px solid; border-radius: 8px; }
        .stat-card.primary { border-left-color: #e91e63; }
        .stat-card.success { border-left-color: #1cc88a; }
        .stat-card.info { border-left-color: #36b9cc; }
        .stat-card.warning { border-left-color: #f6c23e; }
        .stat-card .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #999; }
        .stat-card .stat-value { font-size: 1.5rem; font-weight: 700; color: #333; }
        footer { color: #999; font-size: 0.85rem; text-align: center; padding: 20px; }
    </style>
</head>
<body>
    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="brand">
            <h4><i class="fas fa-utensils"></i> LOKALBITES</h4>
        </div>
        <nav class="mt-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> Categories
            </a>
            <a href="{{ route('admin.merchants.index') }}" class="nav-link {{ request()->routeIs('admin.merchants.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i> Merchants
            </a>
            <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <i class="fas fa-bowl-food"></i> Menus
            </a>
            <a href="{{ route('admin.tags.index') }}" class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Tags
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Orders
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Reviews
            </a>
        </nav>
    </div>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="topbar">
            <span class="user-name"><i class="fas fa-user-circle me-1"></i> Muhammad Abdi Nur Salam</span>
        </div>
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
        <footer>Copyright &copy; LokalBites - {{ date('Y') }}</footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

