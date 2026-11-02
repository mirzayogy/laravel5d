
<!doctype html>
<html lang="en">
<!--begin::Head-->
@include('layouts.components.head')
<!--end::Head-->

<!--begin::Body-->
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    <!--begin::App Wrapper-->
    <div class="app-wrapper">

        <!-- Navbar -->
        @include('layouts.components.nav')

        <!-- Sidebar -->
        @include('layouts.components.sidebar')

        <!--begin::App Main-->
        <main class="app-main">

            <!-- Breadcrumb -->
            @include('layouts.components.breadcrumb')

            <!--begin::App Content-->
<div class="app-content">
    @yield('content')
</div>
<!--end::App Content-->

        </main>
        <!--end::App Main-->

        <!-- Footer -->
        @include('layouts.components.footer')

    </div>
    <!--end::App Wrapper-->

    <!-- Scripts -->
    @include('layouts.components.scripts')
    @stack('scripts')
</body>
<!--end::Body-->
</html>
