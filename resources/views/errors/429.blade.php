<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RCOA</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.jpg') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        html, body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: sans-serif;
            font-weight: 400;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .error-container {
            text-align: center;
            padding: 20px;
        }
        .error-code {
            font-size: 200px;
            font-weight: 700;
            margin: 0;
            color: #99A7AF;
        }
        .error-message {
            font-size: 24px;
            margin: 20px 0;
            color: #E36C5D;
        }
        .error-details {
            font-size: 16px;
            color: #6c757d;
        }
        .back-home {
            margin-top: 20px;
        }
        .back-home a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
        }
        .back-home a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">
            429
        </div>
        <div class="error-message">
        	Too Many Attempts
        </div>
        <div class="error-details">
        	You have exceeded the maximum number of login attempts. Your account has been temporarily locked.
        	<br>
            Please contact the system administrator for assistance.
        </div>
        <div class="back-home">
            <a href="{{route('dashboard.index')}}">Back to Home</a>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(Session::has('error'))
            toastr.error('{{ Session::get('error')}}', 'Administrator', {timeOut: 3000, progressBar: true});
        @elseif(Session::has('success'))
            toastr.success('{{ Session::get('success')}}', 'Administrator', {timeOut: 3000, progressBar: true});
        @endif
    
        // Handle Validation Error Messages
        @if($errors->any())
          @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}', 'Administrator', {timeOut: 5000, progressBar: true});
          @endforeach
        @endif
    </script>
</body>
</html>
