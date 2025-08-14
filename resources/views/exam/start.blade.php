@extends('components.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Enter Exam Token</div>

                <div class="card-body">
                    <p class="text-center">Please enter your exam token to start the exam.</p>
                    <form method="POST" action="{{ route('exam.validate-token') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="token" class="col-md-4 col-form-label text-md-right">Exam Token</label>

                            <div class="col-md-6">
                                <input id="token" type="text" class="form-control @error('token') is-invalid @enderror" name="token" required autofocus>

                                @error('token')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
@endsection

@section('scripts')
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
                    Swal.showLoading(); // Show the loading spinner
                }
            });
        });
    });
</script>
@endsection