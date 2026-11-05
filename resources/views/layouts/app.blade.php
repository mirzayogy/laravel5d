<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | Kost Area</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --black: #000000;
            --brown: #A1887F;
            --brown-dark: #6f5a52;
            --brown-deep: #4a3a34;
            --bg: #e8dcd5;
            --card: #f5ede8;
            --card-soft: #ede1da;
            --head: #e0d0c7;
            --line: #d6c4ba;
            --ink: #1f1714;
            --text: #2b211e;
            --muted: #7d6a63;
            --sage: #7a9a7e;
            --sage-dark: #46684c;
            --terra: #c2714f;
            --terra-dark: #8f4a2d;
            --rose: #b5646f;
            --rose-dark: #7f3a45;
            --gold: #d4a24c;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif; background: linear-gradient(160deg, #eadfd8 0%, #e2d3ca 100%); background-attachment: fixed; color: var(--text); display: flex; min-height: 100vh; }
        a { color: var(--brown-dark); }

        /* Sidebar */
        .sidebar { width: 250px; background: linear-gradient(180deg, #000000 0%, #1c1512 55%, #33261f 100%); color: #d9ccc5; padding: 24px 16px; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .brand { display: flex; align-items: center; gap: 12px; padding: 4px 8px 24px; }
        .brand-logo { width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #c4ada4, var(--brown)); display: grid; place-items: center; font-size: 22px; box-shadow: 0 6px 16px rgba(161,136,127,.4); }
        .brand-name { font-size: 18px; font-weight: 800; color: #fff; line-height: 1.1; }
        .brand-sub { font-size: 11px; color: var(--brown); font-weight: 500; }
        .menu-label { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; color: var(--brown); padding: 14px 12px 8px; font-weight: 700; }
        .sidebar a { display: flex; align-items: center; gap: 12px; padding: 11px 14px; margin-bottom: 4px; color: #d9ccc5; text-decoration: none; font-size: 14.5px; font-weight: 500; border-radius: 12px; transition: all .15s; }
        .sidebar a .ico { font-size: 17px; width: 22px; text-align: center; }
        .sidebar a:hover { background: rgba(161,136,127,.22); color: #fff; }
        .sidebar a.active { background: var(--brown); color: #000; font-weight: 800; box-shadow: 0 6px 16px rgba(161,136,127,.35); }

        /* Konten */
        .content { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 18px 32px; background: rgba(232,220,213,.88); backdrop-filter: blur(8px); border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 5; }
        .topbar .where { font-size: 13px; color: var(--muted); }
        .topbar .where b { color: var(--ink); }
        .user-chip { display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: var(--ink); }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--brown), var(--brown-dark)); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 14px; box-shadow: 0 4px 10px rgba(111,90,82,.35); }
        .main { padding: 32px; max-width: 1280px; width: 100%; }

        h1 { margin: 0 0 6px; font-size: 28px; font-weight: 800; color: var(--ink); letter-spacing: -.02em; }
        .subtitle { color: var(--muted); margin: 0 0 24px; font-size: 14.5px; }
        .subtitle a { text-decoration: none; font-weight: 600; }

        /* Kartu */
        .card { background: var(--card); border-radius: 18px; box-shadow: 0 6px 22px rgba(74,58,52,.12); border: 1px solid var(--line); padding: 24px; margin-bottom: 24px; overflow-x: auto; }
        .card h2 { margin: 0 0 18px; font-size: 17px; font-weight: 700; color: var(--ink); padding-left: 12px; border-left: 4px solid var(--terra); line-height: 1.2; }

        /* Kartu statistik */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 18px; margin-bottom: 24px; }
        .stat { border-radius: 18px; padding: 20px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 10px 24px rgba(74,58,52,.22); }
        .stat .num { font-size: 32px; font-weight: 800; line-height: 1; }
        .stat .lbl { font-size: 13px; opacity: .92; margin-top: 6px; font-weight: 500; }
        .stat .ic { position: absolute; right: 14px; top: 10px; font-size: 38px; opacity: .28; }
        .stat.s1 { background: linear-gradient(135deg, #000000, #3b2f2b); }
        .stat.s2 { background: linear-gradient(135deg, #b9a198, #8c7369); }
        .stat.s3 { background: linear-gradient(135deg, #8fae92, #4f7456); }
        .stat.s4 { background: linear-gradient(135deg, #dc9366, #b25a38); }

        /* Tabel */
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { padding: 12px 14px; text-align: left; font-size: 11.5px; letter-spacing: .06em; text-transform: uppercase; color: var(--brown-deep); font-weight: 800; background: var(--head); border-bottom: 1px solid var(--line); }
        th:first-child { border-radius: 10px 0 0 10px; }
        th:last-child { border-radius: 0 10px 10px 0; }
        td { padding: 14px; border-bottom: 1px solid var(--line); }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover td { background: var(--card-soft); }
        td.num, th.num { text-align: right; }

        /* Tombol */
        .btn { display: inline-block; padding: 7px 15px; background: linear-gradient(135deg, #b39b92, var(--brown-dark)); color: #fff !important; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700; box-shadow: 0 4px 10px rgba(111,90,82,.3); transition: all .12s; }
        .btn:hover { background: linear-gradient(135deg, var(--terra), var(--terra-dark)); transform: translateY(-1px); }
        .btn-gray { background: var(--head); color: var(--brown-deep) !important; box-shadow: none; border: 1px solid var(--line); }
        .btn-gray:hover { background: var(--brown); color: #fff !important; }

        /* Badge */
        .badge { display: inline-block; padding: 4px 11px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge-green { background: #dbe7d8; color: var(--sage-dark); border: 1px solid var(--sage); }
        .badge-yellow { background: #f4dcc6; color: var(--terra-dark); border: 1px solid var(--terra); }
        .badge-red { background: #efd3d6; color: var(--rose-dark); border: 1px solid var(--rose); }
        .badge-blue { background: #d9e6e4; color: #3d6566; border: 1px solid #7fa3a3; }
        .badge-gray { background: #ddd2cc; color: #4a3d38; border: 1px solid #b8a89f; }
        .badge-purple { background: #eadccf; color: #7a5a2a; border: 1px solid var(--gold); }

        /* Detail */
        .detail { display: grid; grid-template-columns: 190px 1fr; gap: 12px 16px; font-size: 14.5px; margin: 0; }
        .detail dt { color: var(--muted); font-weight: 500; }
        .detail dd { margin: 0; font-weight: 600; color: var(--ink); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .empty { color: var(--muted); text-align: center; padding: 28px; }
        .pill { display: inline-block; background: #e3d3c9; color: var(--brown-deep); border: 1px solid var(--line); border-radius: 8px; padding: 3px 9px; font-size: 12px; font-weight: 600; margin: 2px 4px 2px 0; }

        @media (max-width: 900px) {
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 760px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: static; display: flex; flex-wrap: wrap; gap: 4px; padding: 14px; }
            .brand { width: 100%; padding-bottom: 8px; }
            .menu-label { display: none; }
            .sidebar a { padding: 8px 12px; margin: 0; }
            .main { padding: 20px 16px; }
            .topbar { padding: 14px 16px; }
            .detail { grid-template-columns: 1fr; gap: 2px; }
            .detail dd { margin-bottom: 10px; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">🏠</div>
            <div>
                <div class="brand-name">Kost Area</div>
                <div class="brand-sub">Manajemen &amp; Booking Kos</div>
            </div>
        </div>

        <div class="menu-label">Menu Utama</div>
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}"><span class="ico">📊</span> Dashboard</a>
        <a href="{{ route('kosts.index') }}" class="{{ request()->routeIs('kosts.*') ? 'active' : '' }}"><span class="ico">🏘️</span> Kost</a>
        <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}"><span class="ico">🛏️</span> Kamar</a>
        <a href="{{ route('facilities.index') }}" class="{{ request()->routeIs('facilities.*') ? 'active' : '' }}"><span class="ico">✨</span> Fasilitas</a>

        <div class="menu-label">Transaksi</div>
        <a href="{{ route('tenants.index') }}" class="{{ request()->routeIs('tenants.*') ? 'active' : '' }}"><span class="ico">👥</span> Penyewa</a>
        <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}"><span class="ico">📅</span> Booking</a>
        <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}"><span class="ico">💳</span> Pembayaran</a>
    </aside>

    <div class="content">
        <header class="topbar">
            <div class="where">Kost Area / <b>@yield('title', 'Dashboard')</b></div>
            <div class="user-chip">
                <span>Pemilik Kost</span>
                <div class="avatar">P</div>
            </div>
        </header>

        <main class="main">
            @yield('content')
        </main>
    </div>
</body>
</html>