@extends('components.app')

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

                    {{-- <form method="POST" action="{{ route('exam.start-exam') }}">
                        @csrf
                        <div class="form-group row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    Start Exam
                                </button>
                            </div>
                        </div>
                    </form> --}}
                     <div class="text-center">
                        <button type="button" class="btn btn-primary" id="start-exam-btn">
                            Start Exam
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Certification Modal -->
<div class="modal fade" id="certificationModal" tabindex="-1" role="dialog" aria-labelledby="certificationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="certificationModalLabel">Certification</h5>
            </div>
            <div class="modal-body">
                <p class="text-justify">
                    I, <strong>{{ session('examinee_full_name') ?? '' }}</strong>, commit myself to take the online test for Radio Communication and will take the same with all honesty and integrity.
                    I bound myself to be held liable for dishonesty and conduct unbecoming of a police officer if I undertake fraud and misrepresentation in taking the said test.
                </p>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="certify" name="certified" value="1">
                    <label class="form-check-label" for="certify">
                        I certify the above statement
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="start-exam-now-btn">
                    Start Exam Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for actual submission -->
<form id="actual-start-exam-form" method="POST" action="{{ route('exam.start-exam') }}" style="display: none;">
    @csrf
    <input type="hidden" name="certified" value="1">
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Show SweetAlert for success/error messages
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

        // Show modal only when Start Exam button is clicked
        $('#start-exam-btn').on('click', function() {
            $('#certificationModal').modal('show');
        });

        // Start Exam Now button click handler
        $('#start-exam-now-btn').on('click', function() {
            if ($('#certify').is(':checked')) {

                // Show loading spinner
                Swal.fire({
                    title: 'Preparing Exam...',
                    html: 'Please wait while we load your questions...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        setTimeout(() => {
                            $('#actual-start-exam-form').submit();
                        }, 500);
                    }
                });
            } else {
                toastr.error('You must certify the statement before starting the exam.', 'Administrator', {timeOut: 5000, progressBar: true});
            }
        });
    });
</script>
@endsection
