
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PawCare') - Your Pet, Our Care</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --cream: #faf6ee;
            --sage: #718b75;
            --brown: #705c4c;
        }
        body {
            background: var(--cream);
            color: #383b34;
            font-family: system-ui, sans-serif;
        }
        .navbar { background: #fff; }
        .navbar-brand {
            font-weight: 800;
            color: var(--sage);
        }
        .btn-paw {
            background: var(--sage);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
        }
        .btn-paw:hover {
            background: #526e57;
            color: white;
        }
        .paw-card {
            background: white;
            border: 1px solid #ede9e0;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 5px 20px #00000008;
        }
        .form-control { border-radius: 10px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            🐾 PawCare
        </a>

        <div class="d-flex gap-2 align-items-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="btn btn-outline-secondary btn-sm">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="btn btn-paw btn-sm">
                    Daftar
                </a>
            @endauth
        </div>
    </div>
</nav>

<main class="container py-5">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
