<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom Styles --}}
    <style>
        /* Topbar Absolute */
        .topbar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }

        .topbar img {
            height: 40px;
            margin-right: 10px;
        }

        .topbar .brand-name {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        /* Make sure body doesn't get pushed */
        body {
            padding-top: 0 !important;
        }
    </style>
</head>
<body>

    {{-- Topbar --}}
    <div class="topbar">
        <img src="{{ asset('img/logo-laco-1.png') }}" alt="Logo">
        <span class="brand-name">ARGI360 manager</span>
    </div>

    {{-- Content --}}
    @yield('content')

</body>
</html>