
<!--begin::Header-->
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        <!-- Toggle Sidebar -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"
                    aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>

        <!-- Navbar Right -->
        <ul class="navbar-nav ms-auto">

            <!-- Fullscreen -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen"
                    aria-label="Toggle fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
                </a>
            </li>

            <!-- Theme Toggle -->
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="bd-theme"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
                    <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
                    <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item" type="button"
                            data-bs-theme-value="light">
                            <i class="bi bi-sun-fill me-2"></i> Light
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button"
                            data-bs-theme-value="dark">
                            <i class="bi bi-moon-fill me-2"></i> Dark
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button"
                            data-bs-theme-value="auto">
                            <i class="bi bi-circle-half me-2"></i> Auto
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Administrator -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    <span class="d-none d-md-inline">Administrator</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <span class="dropdown-item-text">
                            <strong>Administrator E-Commerce</strong>
                        </span>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>
<!--end::Header-->
