@extends('components.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Exam Results</div>

                <div class="card-body text-center">
                    <h3 class="mb-4">Exam Completed</h3>
                    
                    <div class="result-display mb-4">
                        <div class="score-circle mx-auto mb-3" style="width: 150px; height: 150px; border-radius: 50%; border: 10px solid {{ str_contains($proficiency, 'Failed') ? '#dc3545' : '#28a745' }}; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <h2 class="mb-0">{{ $score }}/{{ $total }}</h2>
                                <small>Score</small>
                            </div>
                        </div>
                        
                        {{-- <h4 class="mt-4">Rating: <span class="badge {{ str_contains($proficiency, 'Failed') ? 'badge-danger' : 'badge-success' }}" style="font-size: 1.2em">{{ $proficiency }}</span></h4> --}}
                        <h4 class="mt-4">
                            <span style="font-size: 1.2em; font-weight: bold; color: {{ str_contains($proficiency, 'Failed') ? '#dc3545' : '#28a745' }};">
                                {{ $proficiency }}
                            </span>
                        </h4>
                    </div>
                    
                    <div class="alert" style="background-color: #e7f4ff; color: #004085; border: 1px solid #007bff; padding: 12px 15px;">
                        Thank you for completing the exam <strong>{{ $examinee }}</strong>. Your results have been recorded.
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
<script>
document.getElementById('finish-form').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: new FormData(this)
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
@endsection