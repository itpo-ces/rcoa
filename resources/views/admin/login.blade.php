<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RCOA</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{asset('adminlte/plugins/toastr/toastr.min.css')}}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css')}}">
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('adminlte/css/style.css')}}">
    <style>
       /* Particles containers */
        #particles-js-login-left,
        #particles-js-login-right {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        /* Left (image) section particles - semi-transparent overlay */
        #particles-js-login-left {
            background-color: rgba(0, 0, 0, 0.3); 
        }

        /* Right (login) section particles - lighter effect */
        #particles-js-login-right {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Ensure content stays above particles */
        .image-section > *:not(#particles-js-login-left),
        .login-section > *:not(#particles-js-login-right) {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <div id="particles-js-login-left"></div>
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo">
            {{-- <h2>Portfolio</h2> --}}
        </div>
        
        <div class="login-section">
            <div id="particles-js-login-right"></div>
            <div class="login-container">
                <div class="login-header">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="brand-image">
                    <h1>Login to AdminPanel</h1>
                    <p class="subtitle">Sign in to start your session</p>
                </div>
                
                <form action="{{route('postLogin')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <div class="email-wrapper">
                            <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                            <i class="fas fa-envelope email-toggle"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="login-btn">Login</button>
                </form>
                
                <div class="signature">
                    <small>Copyright &copy; {{ date('Y')}}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- Toastr -->
    <script src="{{ asset('adminlte/plugins/toastr/toastr.min.js')}}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <script>
        // Password toggle functionality - FIXED VERSION
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.querySelector("#togglePassword");
            const password = document.querySelector("#password");

            togglePassword.addEventListener("click", function() {
                // Toggle the password field type
                const type = password.getAttribute("type") === "password" ? "text" : "password";
                password.setAttribute("type", type);
                
                // Toggle the eye/eye-slash icon CORRECTLY
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            });

            // Toastr notifications
            @if(Session::has('error'))
                toastr.error('{{ Session::get('error') }}', 'Error', {
                    timeOut: 3000,
                    progressBar: true,
                    closeButton: true
                });
            @endif
            
            @if(Session::has('success'))
                toastr.success('{{ Session::get('success') }}', 'Success', {
                    timeOut: 3000,
                    progressBar: true,
                    closeButton: true
                });
            @endif
            
            // SweetAlert validation errors
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `<ul style="text-align: left;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>`,
                    showConfirmButton: true,
                    confirmButtonColor: '#4CAF50'
                });
            @endif
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Left side particles (image section)
            if (document.getElementById('particles-js-login-left')) {
                particlesJS('particles-js-login-left', {
                    particles: {
                        number: { value: 60, density: { enable: true, value_area: 800 } },
                        color: { value: "#ffffff" }, // White particles for dark background
                        opacity: { value: 0.5, random: true },
                        size: { value: 3, random: true },
                        line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
                        move: { enable: true, speed: 2, direction: "none", random: true, straight: false, out_mode: "out" }
                    },
                    interactivity: {
                        detect_on: "canvas",
                        events: {
                            onhover: { enable: true, mode: "repulse" },
                            onclick: { enable: true, mode: "push" }
                        }
                    }
                });
            }

            // Right side particles (login section)
            if (document.getElementById('particles-js-login-right')) {
                particlesJS('particles-js-login-right', {
                    particles: {
                        number: { value: 60, density: { enable: true, value_area: 800 } },
                        color: { value: "#3b82f6" }, // Blue particles for light background
                        opacity: { value: 0.5, random: true },
                        size: { value: 3, random: true },
                        line_linked: { enable: true, distance: 150, color: "#3b82f6", opacity: 0.2, width: 1 },
                        move: { enable: true, speed: 1.5, direction: "none", random: true, straight: false, out_mode: "out" }
                    },
                    interactivity: {
                        detect_on: "canvas",
                        events: {
                            onhover: { enable: true, mode: "repulse" },
                            onclick: { enable: true, mode: "push" }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>