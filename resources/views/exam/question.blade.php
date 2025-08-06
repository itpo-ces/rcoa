@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Question {{ $question_number }} of {{ $total_questions }}</span>
                        <div id="timer" class="badge badge-danger"></div>
                    </div>
                    <div class="progress mt-2">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: {{ ($question_number / $total_questions) * 100 }}%"></div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="answer-form">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">

                        <div class="question mb-4">
                            @if($question->difficulty)
                                @if($question->difficulty === 'easy')
                                    <span class="badge badge-success">Easy</span>
                                @elseif($question->difficulty === 'moderate')
                                    <span class="badge badge-warning">Moderate</span>
                                @elseif($question->difficulty === 'hard')
                                    <span class="badge badge-danger">Hard</span>
                                @elseif($question->difficulty === 'difficult')
                                    <span class="badge badge-dark">Difficult</span>
                                @endif
                            @endif
                            <h5>{{ $question->question_text }}</h5>
                            
                            @if($question->type === 'multiple_choice')
                                <div class="options mt-3">
                                    @php
                                        $options = is_array($question->options) ? $question->options : json_decode($question->options, true) ?? [];
                                        shuffle($options); // <-- this will shuffle the options every time
                                    @endphp

                                    @foreach($options as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer" id="option-{{ $loop->index }}" value="{{ $option }}"
                                                @if(session("answers.{$question->id}") === $option) checked @endif>
                                            <label class="form-check-label" for="option-{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->type === 'true_or_false')
                                <div class="options mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="true" value="True"
                                            @if(session("answers.{$question->id}") === 'True') checked @endif>
                                        <label class="form-check-label" for="true">
                                            True
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="false" value="False"
                                            @if(session("answers.{$question->id}") === 'False') checked @endif>
                                        <label class="form-check-label" for="false">
                                            False
                                        </label>
                                    </div>
                                </div>
                            @elseif($question->type === 'fill_in_the_blanks')
                                <div class="form-group mt-3">
                                    <input type="text" class="form-control" name="answer" value="{{ session("answers.{$question->id}") }}">
                                </div>
                            @endif
                        </div>

                        <div class="navigation-buttons d-flex justify-content-between">
                            @if($question_number > 1)
                                <a href="{{ route('exam.question', ['question_number' => $question_number - 1]) }}" class="btn btn-secondary">Back</a>
                            @else
                                <div></div> <!-- Empty div for spacing -->
                            @endif

                            @if($question_number < $total_questions)
                                <button type="button" class="btn btn-primary" onclick="saveAndContinue()">Next</button>
                            @else
                                <button type="button" class="btn btn-success" onclick="submitExam()">Submit Exam</button>
                            @endif
                        </div>
                    </form>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-justify">I, <strong>{{ session('examinee_full_name') ?? '' }}</strong>, hereby certify that I took the online examination for Radio Communication on August 10, 2025 with honesty and integrity. I affirm that I did not use any unauthorized materials or receive any form of assistance during the exam.</p>
                <p class="text-justify">I understand that any act of dishonesty may result in the invalidation of my exam and possible disciplinary action.</p>
                
                <form id="certification-form" action="{{ route('exam.submit') }}" method="POST">
                    @csrf
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="certify" name="certify" required>
                        <label class="form-check-label" for="certify">
                            I certify the above statement
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('certification-form').submit()">Submit Exam</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    @if(Session::has('error'))
        toastr.error('{{ Session::get('error')}}', 'Administrator', {timeOut: 3000, progressBar: true});
    @elseif(Session::has('success'))
        toastr.success('{{ Session::get('success')}}', 'Administrator', {timeOut: 3000, progressBar: true});
    @endif

    // Handle Validation Error Messages
    @if($errors->any())
      @foreach ($errors->all() as $error)
        toastr.error('{{ $error }}', 'Administrator', {timeOut: 3000, progressBar: true});
      @endforeach
    @endif
</script>
<script>
    // Timer functionality
    let remainingTime = {{ $remaining_time }}; // in seconds

    function updateTimer() {
        if (remainingTime > 0) {
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;
            document.getElementById('timer').textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            document.getElementById('global-timer').textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            remainingTime--; // Decrease every second
        } else {
            document.getElementById('timer').textContent = "0:00";
            toastr.error('Time is up! Your exam will be automatically submitted.');
            document.getElementById('certification-form').submit();
            document.querySelectorAll('button').forEach(btn => btn.disabled = true);
        }
    }

    if (remainingTime > 0) {
        setInterval(updateTimer, 1000);
        updateTimer(); // Initial call to show immediately
    }
    
    // Save answer and continue
    function saveAndContinue() {
        const form = document.getElementById('answer-form');
        const formData = new FormData(form);
        
        fetch('{{ route("exam.save-answer") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json' // This tells Laravel we want JSON back
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("exam.question", ["question_number" => $question_number + 1]) }}';
            }
        })
        .catch(error => {
            if (error.errors) {
                toastr.error(error.errors.join('<br>'), 'Error', {timeOut: 5000, progressBar: true, escapeHtml: false});
            } else {
                toastr.error('An error occurred. Please try again.', 'Error');
            }
        });
    }

    // Submit exam
    function submitExam() {
        const form = document.getElementById('answer-form');
        const formData = new FormData(form);
        
        fetch('{{ route("exam.save-answer") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                $('#certificationModal').modal('show');
            }
        })
        .catch(error => {
            if (error.errors) {
                toastr.error(error.errors.join('<br>'), 'Error', {timeOut: 5000, progressBar: true, escapeHtml: false});
            } else {
                toastr.error('An error occurred. Please try again.', 'Error');
            }
        });
    }
</script>
@endsection