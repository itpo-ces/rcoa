<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RCOA</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}">
    <!-- Font Awesome for eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('adminlte/css/style.css')}}">
    <style>
        .tfa-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .tfa-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .tfa-header {
            text-align: center;
            margin-bottom: 1rem;
        }

        .tfa-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        .qr-code {
            margin: 20px 0px 20px 0px;
            border: 2px dashed #007bff;
            padding: 10px 20px 10px 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
        }
        .text-normal {
            text-align: justify;
            font-size: 14px;
            font-weight: normal;
            color: #0e0d0d;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .text-scan {
            text-align: center;
            font-size: 14px;
            font-weight: normal;
            color: red;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <img src="{{ asset('images/logo.jpg') }}" class="img-fluid shadow-lg" alt="Logo">
            {{-- <h2>LMIS</h2> --}}
        </div>
        
        <div class="tfa-section">
            <div class="tfa-container">
                <div class="tfa-header">
                    <img src="{{ asset('images/GA.png') }}" alt="Google Authenticator" class="brand-image">
                    <h1>2FA Registration</h1>
                    <p class="text-scan">Please <strong>scan the QR</strong> Code below using Google Authenticator Mobile App.</p>
                </div>

                @if (empty($imageUrl))
                    <div class="alert alert-danger">
                        QR_Image is not available. Please check the controller.
                    </div>
                @else
                    <div class="qr-code">
                        <img src="data:image/png;base64,{{ $imageUrl }}" alt="QR Code" class="img-fluid">
                    </div>
                @endif

                <p class="text-normal">You need to set up your Google Authenticator Mobile App before continuing. <strong>You will be unable to login otherwise.</strong></p>
                <div class="text-center">
                    <form action="{{ route('google2fa.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="secret" value="{{ $secret }}">
                        <button type="submit" class="login-btn">Complete Login</button>
                    </form>
                </div>
                <div class="signature">
                    <small>Copyright &copy; <span id="currentYear"></span></small>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Password toggle functionality - FIXED VERSION
        document.addEventListener("DOMContentLoaded", function() {
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
        // Get the current year dynamically and set it in the span
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
</body>
</html>