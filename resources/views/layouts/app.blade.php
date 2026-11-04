<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko Online Mini')</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f9; color: #212529; }
        nav { background: #343a40; padding: 0 24px; display: flex; align-items: center; gap: 8px; }
        nav .brand { color: #fff; font-weight: 700; margin-right: 24px; padding: 14px 0; }
        nav a { color: #c2c7d0; text-decoration: none; padding: 14px 12px; }
        nav a.active, nav a:hover { color: #fff; background: #4b545c; }
        main { max-width: 960px; margin: 24px auto; padding: 0 16px; }
        h1 { margin-top: 0; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .card { background: #fff; border-radius: 6px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
        .card .num { font-size: 32px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
        th, td { padding: 10px 12px; border-bottom: 1px solid #dee2e6; text-align: left; }
        th { background: #e9ecef; }
        .right { text-align: right; }
        .badge { background: #17a2b8; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 12px; }
        .order { margin-bottom: 24px; }
    </style>
</head>
<body>
    <nav>
        <span class="brand">Toko Online Mini</span>
        <a href="{{ route('toko.index') }}" class="{{ request()->routeIs('toko.index') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('toko.produk') }}" class="{{ request()->routeIs('toko.produk') ? 'active' : '' }}">Produk</a>
        <a href="{{ route('toko.pesanan') }}" class="{{ request()->routeIs('toko.pesanan') ? 'active' : '' }}">Pesanan</a>
    </nav>
    <main>
        @yield('content')
    </main>
</body>
</html>