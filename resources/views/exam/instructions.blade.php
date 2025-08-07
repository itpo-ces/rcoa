@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Exam Instructions</div>

                <div class="card-body">
                    <h4>Rules for Taking an Online Exam:</h4>
                   <ol>
                        <li><strong>Ensure a Stable Internet Connection</strong><br>
                            Test your internet connection before the exam starts to avoid interruptions.</li>

                        <li><strong>Use a Reliable Device</strong><br>
                            Use a fully charged laptop, desktop, tablet or mobile phones.</li>

                        <li><strong>Be in a Quiet and Well-Lit Room</strong><br>
                            Choose a quiet environment free from distractions and noise. Lighting should clearly show your face during the exam.</li>

                        <li><strong>Log in Early</strong><br>
                            Join the exam platform at least 15 minutes before the scheduled time to resolve any technical issues.</li>

                        <li><strong>Follow Exam Platform Instructions</strong><br>
                            Carefully read and follow instructions provided on the screen or by the proctor.</li>

                        <li><strong>No Unauthorized Materials</strong><br>
                            Do not use notes, books, calculators, or other devices unless specifically allowed.</li>

                        <li><strong>Camera and Microphone Must Be On (if required)</strong><br>
                            Keep your webcam and microphone on throughout the exam if proctoring is required. Your face must remain visible.</li>

                        <li><strong>No Switching Tabs or Screens</strong><br>
                            Avoid opening new tabs, windows, or applications. Screen monitoring software may detect violations.</li>

                        <li><strong>Do Not Communicate with Others</strong><br>
                            No messaging, phone calls, or talking to anyone while the exam is ongoing. It may be considered cheating.</li>

                        <li><strong>Submit Before the Deadline</strong><br>
                            Ensure you click “Submit” before the time runs out. Late submissions may not be accepted.</li>

                        <li><strong>Report Technical Issues Immediately</strong><br>
                            Inform the proctor or instructor right away if you experience technical problems.</li>

                        <li><strong>Maintain Academic Integrity</strong><br>
                            Follow all ethical standards. Any attempt to cheat or manipulate the system may result in penalties or disqualification.</li>
                    </ol>

                    <form method="POST" action="{{ route('exam.start-exam') }}">
                        @csrf
                        <div class="form-group row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    Start Exam
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
                title: 'Loading Questions...',
                html: 'Please wait while we load the questions...',
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