@extends('components.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Certification</h5>
                </div>
                <div class="card-body">
                    <p class="text-justify">I, <strong>{{ session('examinee_full_name') ?? '' }}</strong>, affirm that I personally took the online test for Radio Communication without any forms of assistance, whatsoever, during the test.</p>
                    
                    <form id="certification-form" action="{{ route('exam.submit') }}" method="POST">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="certify" name="certify" required>
                            <label class="form-check-label" for="certify">
                                I certify the above statement
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary" id="final-submit-btn" disabled>Submit Exam</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const certifyCheckbox = document.getElementById('certify');
        const submitBtn = document.getElementById('final-submit-btn');

        document.getElementById('global-timer').textContent = "0:00";
        
        certifyCheckbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });
    });
</script>
@endsection