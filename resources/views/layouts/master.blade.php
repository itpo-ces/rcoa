<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RCOA | AdminPanel</title>
    <link rel="shortcut icon" href="{{asset('images/logo.jpg')}}" type="image/x-icon">

    @include('layouts.header')

    @yield('links')

    @yield('css')
    
    <style>
        .hidden {
            display: none;
        }
        .modal-title {
            text-align: center !important;
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini sidebar-fixed">

    <div class="wrapper">

         <!-- Preloader -->
        {{-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{asset('images/pnp_hss_logo.jpg')}}" alt="HSS Logo" height="60" width="60">
        </div> --}}

        @include('layouts.nav')
        <div class="content-wrapper">
            @yield('breadcrumb')
            <section class="content">
                @yield('content')
            </section>
        </div>

        <footer class="main-footer no-print">
          <strong>Copyright &copy; {{ date('Y') }} PNP CES.</strong>
          All rights reserved.
          <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0
          </div>
        </footer>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    
    @include('layouts.script')
    
    @yield('script')
</body>
</html>
