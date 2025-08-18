@extends('components.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Enter Exam Token</div>

                <div class="card-body">
                    @if($examinee && $examinee->last_question_number)
                        <div class="alert alert-info mt-4">
                            <p>You have an exam in progress. Would you like to resume?</p>
                            <a href="{{ route('exam.resume') }}" class="btn btn-primary">
                                Resume Exam (Question {{ $examinee->last_question_number }})
                            </a>
                        </div>
                    @endif
                    <p class="text-center">Please enter your exam token to start the exam.</p>
                    <form method="POST" action="{{ route('exam.validate-token') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="token" class="col-md-4 col-form-label text-md-right">Exam Token</label>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="token" type="text" class="form-control @error('token') is-invalid @enderror" name="token" required autofocus>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="scanQRBtn">
                                            <i class="fas fa-qrcode"></i> Scan
                                        </button>
                                    </div>
                                    @error('token')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Click the scan button to use your camera</small>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Validate Token
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden video element for QR scanning -->
<div class="d-none">
    <video id="qrScanner" width="300" height="200" playsinline></video>
</div>
@endsection

@section('scripts')
<!-- Include QR Scanner Library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    $(document).ready(function() {
        // Show SweetAlert message if session has success or error
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}"
            });
        @endif

        // Attach submit event to the form
        $('form').on('submit', function(e) {
            Swal.fire({
                title: 'Validating...',
                html: 'Please wait while we validate the exam code...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // QR Code Scanner Functionality
        const scanQRBtn = document.getElementById('scanQRBtn');
        const tokenInput = document.getElementById('token');
        const videoElement = document.getElementById('qrScanner');
        let qrScannerActive = false;
        let stream = null;

        scanQRBtn.addEventListener('click', function() {
            if (qrScannerActive) {
                stopScanner();
                return;
            }

            Swal.fire({
                title: 'Scan QR Code',
                html: `
                    <div class="text-center">
                        <video id="scannerVideo" width="300" height="200" playsinline class="img-fluid mb-3"></video>
                        <p class="text-muted">Point your camera at the QR code</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Stop Scanning',
                cancelButtonText: 'Close',
                didOpen: () => {
                    const scannerVideo = document.getElementById('scannerVideo');
                    startScanner(scannerVideo);
                },
                willClose: () => {
                    stopScanner();
                }
            });
        });

        function startScanner(videoElement) {
            qrScannerActive = true;
            
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    videoElement.srcObject = stream;
                    videoElement.play();
                    
                    const canvasElement = document.createElement("canvas");
                    const canvas = canvasElement.getContext("2d");
                    
                    function scanQR() {
                        if (!qrScannerActive) return;
                        
                        if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
                            canvasElement.height = videoElement.videoHeight;
                            canvasElement.width = videoElement.videoWidth;
                            canvas.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
                            
                            const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "dontInvert",
                            });
                            
                            if (code) {
                                tokenInput.value = code.data;
                                stopScanner();
                                Swal.close();
                            }
                        }
                        
                        requestAnimationFrame(scanQR);
                    }
                    
                    scanQR();
                })
                .catch(function(err) {
                    console.error("Error accessing camera: ", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Camera Error',
                        text: 'Could not access camera. Please make sure you have granted camera permissions.'
                    });
                });
        }

        function stopScanner() {
            qrScannerActive = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }
    });
</script>
@endsection