<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('img/logo-laco-1.png') }}">

    {{-- ========================== Bootstrap & Icon ========================== --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- ========================== Boxicons ========================== --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">

    {{-- ========================== jQuery UI ========================== --}}
    <link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet">

    {{-- ========================== Select2 ========================== --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet">

    {{-- ========================== DataTables ========================== --}}
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" rel="stylesheet">

    {{-- ========================== Custom CSS ========================== --}}
    <link href="{{ asset('assets/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

    {{-- ========================== Sidebar ========================== --}}
    <div class="sidebar close">
        <div class="logo-details">
            <i class='bx bx-leaf'></i>
            <span class="logo_name">ARGI360</span>
        </div>
        @auth
            <ul class="nav-links">
            @if (Auth::user()->group_id == 3)
                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bxs-cog"></i><span class="link_name">Admin</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">Admin</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Core Admin</a></li>
                        <li><a href="#">Crops</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->group_id == 7 || Auth::user()->group_id == 3)

                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bxs-briefcase"></i><span class="link_name">AE Main</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">AE Main</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Crops</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->group_id == 7 || Auth::user()->group_id == 3)

                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bxs-help-circle"></i><span class="link_name">AE Support</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">AE Support</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Crops</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->group_id == 6 || Auth::user()->group_id == 3)
                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bxs-check-shield"></i><span class="link_name">QA Main</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">AE Main</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Crops</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->group_id == 7 || Auth::user()->group_id == 3)
                <li>
                    <div class="iocn-link">
                        <a href="#"><i class='bx bx-user-check'></i><span class="link_name">QC Main</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">QC Main</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Crops</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->group_id == 7 || Auth::user()->group_id == 3)

                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bxs-star"></i><span class="link_name">VIP</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">VIP</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>

            @endif
                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bxs-grid-alt"></i><span class="link_name">Other</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href="#">Other</a></li>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Reports</a></li>
                    </ul>
                </li>
                <li>
                    <div class="iocn-link">
                        <a href="/logout"><i class="bx bx-log-out"></i><span class="link_name">Log out</span></a>
                    </div>
                </li>

            </ul>

        @endauth
    </div>

    {{-- ========================== Content ========================== --}}
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">@yield('topic')</span>
        </div>

        <div class="container-fluid">
            @yield('content')
        </div>
    </section>

    {{-- ========================== JavaScript ========================== --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script src="{{ asset('assets/sidebar_script.js') }}"></script>

    {{-- ========================== Confirm Function ========================== --}}
    <script>
        function myFunction() {
            if (!confirm("Are You Sure to delete this")) {
                event.preventDefault();
            }
        }
    </script>

    @stack('scripts')
</body>

</html>
