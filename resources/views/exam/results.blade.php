@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Exam Results</div>

                <div class="card-body text-center">
                    <h3 class="mb-4">Exam Completed</h3>
                    
                    <div class="result-display mb-4">
                        <div class="score-circle mx-auto mb-3" style="width: 150px; height: 150px; border-radius: 50%; border: 10px solid #4CAF50; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <h2 class="mb-0">{{ $score }}/{{ $total }}</h2>
                                <small>Score</small>
                            </div>
                        </div>
                        
                        <h4 class="mt-4">Proficiency Level: <span class="badge badge-primary">{{ $proficiency }}</span></h4>
                    </div>
                    
                    <div class="alert alert-info">
                        Thank you for completing the exam {{ $examinee }}. Your results have been recorded.
                    </div>
                    
                    <form action="{{ route('exam.finish') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Return to Home</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // make the id=g-timer element invisible
    document.getElementById('g-timer').style.display = 'none';
</script>
@endsection