<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text font-weight-light pl-3">PenyewaanCamp</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header">DATA MASTER</li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Kategori</p></a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-campground"></i><p>Barang</p></a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-calendar-alt"></i><p>Periode</p></a></li>
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>Penyewaan</p></a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-money-bill"></i><p>Pembayaran</p></a></li>
            </ul>
        </nav>
    </div>
</aside>