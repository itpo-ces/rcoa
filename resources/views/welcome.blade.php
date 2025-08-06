<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Radio Communication Online Assessment</title>

    <link rel="shortcut icon" href="{{asset('images/logo.jpg')}}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome (icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE (includes Bootstrap 4 and basic layout styles) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    
    <!-- Optional: Toastr for flash messages -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <!-- Optional: Sweet Alert for modals -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #343a40;
        }
        .assessment-box {
            max-width: 500px;
            margin: auto;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 justify-content-center align-items-center">

    <!-- Header -->
    <header class="w-100 text-end p-3">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-dark btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-dark btn-sm">Register</a>
                @endif
            @endauth
        @endif
    </header>

    <!-- Main Content -->
    <main class="assessment-box bg-white p-5 shadow rounded">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
        </div>
        <h1 class="h3 mb-3 text-center">Welcome to the <br>Radio Communication Online Assessment</h1>
        <p class="text-muted text-center">This assessment will test your knowledge of radio communication protocols and procedures.</p>
        <div class="text-center mt-4">
            <a href="{{ route('exam.start') }}" class="btn btn-primary btn-lg">Start Assessment</a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-muted mt-auto p-3 small text-center">
        &copy; {{ date('Y') }} Radio Communication Online Assessment
    </footer>

    <!-- REQUIRED JS: jQuery and Bootstrap Bundle (included in AdminLTE) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>

    <!-- Optional: Toastr for notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Optional: Sweet Alert for modals -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}"
            });
            @endif
            @if(session('error'))
            Swal.fire({
                icon: 'info',
                title: 'Admin Notice',
                text: "{{ session('error') }}"
            });
            @endif
        });
    </script>
</body>
</html>
