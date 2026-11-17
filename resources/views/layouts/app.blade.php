<!doctype html>
<html lang="en">
@include('layouts.components.head')
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        @include('layouts.components.nav')
        @include('layouts.components.sidebar')
        <!--begin::App Main-->
        <main class="app-main">
            @yield('breadcrumb')
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!-- Info boxes -->
                    <div class="row">
                        @yield('content')
                    </div>
                    <!-- /.row -->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Container-->
    </div>
    <!--end::App Content-->
    </main>
    <!--end::App Main-->
    @include('layouts.components.footer')
    </div>
    <!--end::App Wrapper-->
    @include('layouts.components.scripts')
</body>
<!--end::Body-->

</html>
