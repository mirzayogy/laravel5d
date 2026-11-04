<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Keuangan')</title>
    
    <!-- Memanggil CSS dari folder public -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <!-- Memanggil bagian sidebar -->
    @include('layouts.sidebar')

    <!-- Area Konten Utama -->
    <div class="main-content">
        <!-- Lubang yang nanti diisi oleh halaman lain -->
        @yield('content')
    </div>

</body>
</html>