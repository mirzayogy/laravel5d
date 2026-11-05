<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko Online Mini') | Toko Online Mini</title>
    <style>
        :root {
            --sidebar: #1e2a4a;
            --sidebar-dark: #141c33;
            --bg: #f3f5fb;
            --text: #2d3748;
            --muted: #8392a5;
            --border: #e6eaf3;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", system-ui, -apple-system, Roboto, sans-serif; background: var(--bg); color: var(--text); }
        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 240px; flex-shrink: 0; background: linear-gradient(180deg, var(--sidebar), var(--sidebar-dark)); color: #cbd5f0; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        .brand { padding: 22px 20px; font-size: 18px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .brand .logo { background: linear-gradient(135deg, #4e73df, #36b9cc); width: 38px; height: 38px; border-radius: 10px; display: grid; place-items: center; font-size: 20px; }
        .menu { padding: 16px 12px; display: flex; flex-direction: column; gap: 4px; }
        .menu-title { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; color: #7f8db3; padding: 8px 12px; }
        .menu a { color: #cbd5f0; text-decoration: none; padding: 11px 14px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-size: 15px; transition: .15s; }
        .menu a:hover { background: rgba(255,255,255,.08); color: #fff; }
        .menu a.active { background: linear-gradient(90deg, #4e73df, #6a8cf0); color: #fff; box-shadow: 0 4px 12px rgba(78,115,223,.4); }
        .sidebar-footer { margin-top: auto; padding: 16px 20px; font-size: 12px; color: #7f8db3; border-top: 1px solid rgba(255,255,255,.08); }

        /* Konten */
        .content { flex: 1; min-width: 0; }
        .topbar { background: #fff; padding: 14px 28px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); box-shadow: 0 1px 4px rgba(0,0,0,.04); }
        .crumb { color: var(--muted); font-size: 14px; }
        .crumb strong { color: var(--text); }
        .user { display: flex; align-items: center; gap: 10px; font-size: 14px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #f6c23e, #e74a3b); color: #fff; display: grid; place-items: center; font-weight: 700; flex-shrink: 0; }
        main { padding: 28px; max-width: 1100px; }
        .page-title h1 { margin: 0 0 4px; font-size: 26px; }
        .page-title p { margin: 0 0 24px; color: var(--muted); }

        /* Kartu statistik */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat { border-radius: 14px; padding: 20px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 6px 16px rgba(0,0,0,.12); }
        .stat .icon { position: absolute; right: 16px; top: 14px; font-size: 40px; opacity: .35; }
        .stat .num { font-size: 36px; font-weight: 700; line-height: 1.1; }
        .stat .label { font-size: 14px; opacity: .95; margin-top: 4px; }
        .stat.blue { background: linear-gradient(135deg, #4e73df, #6f8ff0); }
        .stat.green { background: linear-gradient(135deg, #1cc88a, #4fdcab); }
        .stat.orange { background: linear-gradient(135deg, #f6a21e, #f8c14e); }
        .stat.purple { background: linear-gradient(135deg, #8e5ce6, #b18af0); }

        /* Panel dan tabel */
        .panel { background: #fff; border-radius: 14px; box-shadow: 0 2px 10px rgba(30,42,74,.07); overflow: hidden; margin-bottom: 24px; }
        .panel-head { padding: 16px 20px; border-bottom: 1px solid var(--border); font-weight: 600; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fc; color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: 12px 20px; }
        td { padding: 13px 20px; border-top: 1px solid var(--border); }
        tbody tr:hover { background: #f8faff; }
        .right { text-align: right; }
        .total-row td { background: #f1f5ff; font-weight: 700; }

        /* Badge */
        .badge { display: inline-block; padding: 3px 11px; border-radius: 999px; font-size: 12px; font-weight: 600; color: #fff; }
        .cat-0 { background: #4e73df; }
        .cat-1 { background: #1cc88a; }
        .cat-2 { background: #f59e0b; }
        .cat-3 { background: #e74a3b; }
        .cat-4 { background: #36b9cc; }
        .b-pending { background: #f59e0b; }
        .b-paid { background: #1cc88a; }
        .b-shipped { background: #4e73df; }
        .b-completed { background: #36b9cc; }
        .b-cancelled { background: #e74a3b; }
        .stock { font-weight: 600; padding: 2px 10px; border-radius: 8px; }
        .stock.ok { background: #e6f9f1; color: #12a06b; }
        .stock.low { background: #fdecea; color: #d23b2c; }
        .price { font-weight: 600; }

        /* Bar kategori */
        .bar { background: #e9edf7; border-radius: 999px; height: 10px; width: 100%; min-width: 120px; }
        .fill { height: 10px; border-radius: 999px; }

        /* Pesanan */
        .order-head { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; border-bottom: 1px solid var(--border); }
        .order-head .who { display: flex; align-items: center; gap: 12px; }
        .order-no { font-size: 17px; font-weight: 700; }
        .meta { color: var(--muted); font-size: 13px; }
        .order-grand { font-size: 18px; font-weight: 700; color: #4e73df; }

        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: static; }
            .menu { flex-direction: row; flex-wrap: wrap; }
            .menu-title, .sidebar-footer { display: none; }
            main { padding: 16px; }
            .topbar { padding: 12px 16px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand"><span class="logo">🛒</span> Toko Online</div>
        <nav class="menu">
            <div class="menu-title">Menu</div>
            <a href="{{ route('toko.index') }}" class="{{ request()->routeIs('toko.index') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('toko.produk') }}" class="{{ request()->routeIs('toko.produk') ? 'active' : '' }}">🛍️ Produk</a>
            <a href="{{ route('toko.pesanan') }}" class="{{ request()->routeIs('toko.pesanan') ? 'active' : '' }}">🧾 Pesanan</a>
        </nav>
        <div class="sidebar-footer">Toko Online Mini<br>PBO 2 · TI 5D REG BJB</div>
    </aside>

    <div class="content">
        <header class="topbar">
            <div class="crumb">Toko Online Mini / <strong>@yield('title')</strong></div>
            <div class="user"><span>Norhayati</span><span class="avatar">N</span></div>
        </header>
        <main>
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>