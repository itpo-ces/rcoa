@extends('components.app')

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
                                @elseif($question->difficulty === 'difficult')
                                    <span class="badge badge-danger">Difficult</span>
                                @elseif($question->difficulty === 'extra_difficult')
                                    <span class="badge badge-dark">Extra Difficult</span>
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
                            @elseif($question->type === 'yes_or_no')
                                <div class="options mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="yes" value="Yes"
                                            @if(session("answers.{$question->id}") === 'Yes') checked @endif>
                                        <label class="form-check-label" for="true">
                                            Yes
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="no" value="No"
                                            @if(session("answers.{$question->id}") === 'No') checked @endif>
                                        <label class="form-check-label" for="false">
                                            No
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
                                <button type="button" class="btn btn-success" onclick="validateAndSubmit()">Submit Exam</button>
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
            </div>
            <div class="modal-body">
                <p class="text-justify">I, <strong>{{ session('examinee_full_name') ?? '' }}</strong>, affirm that I personally took the online test for Radio Communication without any forms of assistance, whatsoever, during the test.</p>
                
                <form id="certification-form">
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
                {{-- <button type="button" class="btn btn-primary" id="final-submit-btn" disabled>Submit Exam</button> --}}
                <button type="button" class="btn btn-primary" id="final-submit-btn" disabled onclick="submitFinalExam()">Submit Exam</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Toastr notifications
    @if(Session::has('error'))
        toastr.error('{{ Session::get('error')}}', 'Administrator', {timeOut: 3000, progressBar: true});
    @elseif(Session::has('success'))
        toastr.success('{{ Session::get('success')}}', 'Administrator', {timeOut: 3000, progressBar: true});
    @endif

    @if($errors->any())
      @foreach ($errors->all() as $error)
        toastr.error('{{ $error }}', 'Administrator', {timeOut: 3000, progressBar: true});
      @endforeach
    @endif
</script>
<script>
    // Timer functionality
    let remainingTime = {{ $remaining_time }}; // in seconds
    let timerInterval;
    let timeExpired = false;

    function updateTimer() {
        if (remainingTime > 0) {
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;
            document.getElementById('timer').textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            document.getElementById('global-timer').textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            remainingTime--;
        } else if (!timeExpired) {
            timeExpired = true;
            handleTimeExpired();
        }
    }

    function handleTimeExpired() {
        clearInterval(timerInterval);
        document.getElementById('timer').textContent = "0:00";
        document.getElementById('global-timer').textContent = "0:00";
        
        // Disable all navigation buttons
        document.querySelectorAll('.navigation-buttons a, .navigation-buttons button').forEach(btn => {
            btn.disabled = true;
        });

        // Show "Time is up" message
        toastr.error('⏰ Time is up! Saving your last answer...', 'Exam Ended', { timeOut: 2000, progressBar: true });

        // Save last answer, then wait 3 seconds before redirect
        saveCurrentAnswer()
            .finally(() => {
                setTimeout(() => {
                    window.location.href = '{{ route("exam.certification") }}';
                }, 3000); // 3 seconds
            });
        
        // // Show certification modal immediately (don't wait for save)
        // showCertificationModal('Time is up! Please certify and submit your exam.');
        // Redirect to certification page
        // window.location.href = '{{ route("exam.certification") }}';
    }

    function initTimer() {
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    }

    function showCertificationModal(message) {
        $('#certificationModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        
        if (message) {
            toastr.error(message);
        }
    }

    function saveCurrentAnswer() {
        return new Promise((resolve, reject) => {
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
                    resolve();
                } else {
                    reject(new Error('Failed to save answer'));
                }
            })
            .catch(error => {
                reject(error);
            });
        });
    }

    // function saveAndContinue() {
    //     const form = document.getElementById('answer-form');
    //     const formData = new FormData(form);

    //     // Add current question number to form data
    //     formData.append('current_question', {{ $question_number }});
        
    //     fetch('{{ route("exam.save-answer") }}', {
    //         method: 'POST',
    //         body: formData,
    //         headers: {
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //             'Accept': 'application/json'
    //         }
    //     })
    //     .then(response => {
    //         if (!response.ok) {
    //             return response.json().then(err => Promise.reject(err));
    //         }
    //         return response.json();
    //     })
    //     .then(data => {
    //         if (data.success) {
    //             window.location.href = '{{ route("exam.question", ["question_number" => $question_number + 1]) }}';
    //         }
    //     })
    //     .catch(error => {
    //         if (error.errors) {
    //             toastr.error(error.errors.join('<br>'), 'Error', {timeOut: 5000, progressBar: true, escapeHtml: false});
    //         } else {
    //             toastr.error('An error occurred. Please try again.', 'Error');
    //         }
    //     });
    // }

    function saveAndContinue() {
        const form = document.getElementById('answer-form');
        const formData = new FormData(form);

        // Add current question number to form data (1-based)
        formData.append('current_question', {{ $question_number }});
        
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

    function validateAndSubmit() {
        saveCurrentAnswer().then(() => {
            showCertificationModal();
        }).catch(error => {
            toastr.error('Error saving answer: ' + error.message);
        });
    }

    function submitFinalExam() {
        const certifyCheckbox = document.getElementById('certify');
        const submitBtn = document.getElementById('final-submit-btn');
        
        if (!certifyCheckbox.checked) {
            toastr.error('You must certify the statement before submitting.', 'Error');
            return;
        }
        
        // Show loading state
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
        
        // First save current answer (important for time-expired cases)
        saveCurrentAnswer()
            .then(() => {
                // Then submit the exam
                return fetch('{{ route("exam.submit") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        certify: true,
                        time_expired: timeExpired
                    })
                });
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    throw new Error('Invalid response from server');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                toastr.error('Submission failed. Please try again.', 'Error');
                console.error('Submission error:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initTimer();
        
        document.getElementById('certify')?.addEventListener('change', function() {
            const submitBtn = document.getElementById('final-submit-btn');
            if (submitBtn) {
                submitBtn.disabled = !this.checked;
            }
        });
        
        $('#certificationModal').on('show.bs.modal', function () {
            clearInterval(timerInterval);
            document.getElementById('timer').textContent = "0:00";
        });
    });
</script>
@endsection