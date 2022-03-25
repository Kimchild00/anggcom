<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/logo.png" rel="icon">
    <title>Anggaran COM - Dashboard</title>
    <link href="{{ asset('asset_backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset_backend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset_backend/css/ruang-admin.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo/logo2.png">
            </div>
            <div class="sidebar-brand-text mx-3">Anggaran COM</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="{{ url('backend/dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">
            Features
        </div>

        <li class="nav-item">
            <a class="nav-link" href="{{ url('backend/user-company') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>User Company</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('backend/user') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>User</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('backend/transaction') }}">
                <i class="fas fa-fw fa-list"></i>
                <span>Transaction</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('backend/division') }}">
                <i class="fas fa-fw fa-list"></i>
                <span>Division</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('backend/inquiry') }}">
                <i class="fas fa-fw fa-list"></i>
                <span>Inquiry</span>
            </a>
        </li>
    </ul>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- TopBar -->
            <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
                <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <img class="img-profile rounded-circle" src="img/boy.png" style="max-width: 60px">
                            <span class="ml-2 d-none d-lg-inline text-white small">Maman Ketoprak</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Activity Log
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="logout()">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                            
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- Topbar -->

            @include('notif-alert')
            @yield('content')
        </div>
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
            <span>copyright &copy; <script> document.write(new Date().getFullYear()); </script> - developed by
              <b><a href="https://importir.org/" target="_blank">IMPORTIR.ORG</a></b>
            </span>
                </div>
            </div>
        </footer>
        <!-- Footer -->
    </div>
</div>

<!-- Scroll to top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="{{ asset('asset_backend/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('asset_backend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('asset_backend/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('asset_backend/js/ruang-admin.min.js') }}"></script>
<script src="{{ asset('asset_backend/vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('asset_backend/js/demo/chart-area-demo.js') }}"></script>
<script>
    function logout() {
        fetch('{{ url("/backend/logout") }}', {
            headers: {
                'content-type'      : 'application/json',
                'Accept'            : 'application/json',
                'X-Requested-With'  : 'XMLHttpRequest',
                'X-CSRF-Token'      : '{{ csrf_token() }}'
            },
            method: 'POST',
        })
        .then(res => {
            window.location.reload()
        })
    }
</script>
</body>
</html>
