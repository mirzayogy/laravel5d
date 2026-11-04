{{-- Terapkan tema tersimpan sedini mungkin (mengurangi kedip) --}}
<script>
    (function () {
        try {
            var t = localStorage.getItem('uangku-theme');
            if (t === 'dark' || t === 'light') document.documentElement.setAttribute('data-theme', t);
        } catch (e) {}
    })();
</script>

<aside class="sidebar" aria-label="Navigasi utama">
    <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-label="Tampilkan atau sembunyikan menu">

    <div class="sidebar-top">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-mark"><x-icon name="wallet" size="20" /></span>
            <span>UangKu</span>
        </a>

        <div class="sidebar-actions">
            <button type="button" id="theme-toggle" class="icon-btn icon-btn-dark theme-toggle"
                    aria-label="Ganti tema terang atau gelap" aria-pressed="false" title="Ganti tema">
                <x-icon name="moon" class="icon-moon" />
                <x-icon name="sun" class="icon-sun" />
            </button>
            <label for="nav-toggle" class="icon-btn icon-btn-dark nav-burger" aria-hidden="true">
                <x-icon name="menu" />
            </label>
        </div>
    </div>

    <nav class="nav" aria-label="Menu">
        <span class="nav-label">Menu</span>

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           @if (request()->routeIs('dashboard')) aria-current="page" @endif>
            <x-icon name="layout-dashboard" />
            <span>Dashboard</span>
        </a>

        <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}"
           @if (request()->routeIs('accounts.*')) aria-current="page" @endif>
            <x-icon name="wallet" />
            <span>Kelola Akun</span>
        </a>

        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
           @if (request()->routeIs('transactions.*')) aria-current="page" @endif>
            <x-icon name="arrow-left-right" />
            <span>Kelola Transaksi</span>
        </a>
    </nav>
</aside>

<script>
    (function () {
        var btn = document.getElementById('theme-toggle');
        if (!btn) return;
        var root = document.documentElement;
        btn.setAttribute('aria-pressed', root.getAttribute('data-theme') === 'dark' ? 'true' : 'false');
        btn.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            btn.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
            try { localStorage.setItem('uangku-theme', next); } catch (e) {}
        });
    })();
</script>
