<div class="row">
    <div class="col-md-12">
        <h4>Question Text</h4>
        <p>{{ $question->question_text }}</p>
        
        <h4 class="mt-4">Statistics</h4>
        <table class="table table-bordered">
            <tr>
                <th>Total Responses</th>
                <td>{{ $question->total_responses }}</td>
            </tr>
            <tr>
                <th>Correct Responses</th>
                <td>{{ $question->correct_responses }} ({{ $correctPercentage }}%)</td>
            </tr>
            <tr>
                <th>Incorrect Responses</th>
                <td>{{ $incorrectResponses }} ({{ $incorrectPercentage }}%)</td>
            </tr>
            <tr>
                <th>Question Type</th>
                <td>{{ $question->type_label }}</td>
            </tr>
            <tr>
                <th>Difficulty</th>
                <td>{{ $question->difficulty_label }}</td>
            </tr>
            <tr>
                <th>Correct Answer</th>
                <td>{{ $question->correct_answer }}</td>
            </tr>
        </table>

        @if($commonIncorrect->isNotEmpty())
        <h4 class="mt-4">Common Incorrect Answers</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Answer</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commonIncorrect as $incorrect)
                <tr>
                    <td>{{ $incorrect->response }}</td>
                    <td>{{ $incorrect->count }}</td>
                    <td>{{ $question->total_responses > 0 ? round(($incorrect->count / $question->total_responses) * 100) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <h4 class="mt-4">Performance Visualization</h4>
        <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: {{ $correctPercentage }}%" 
                aria-valuenow="{{ $correctPercentage }}" aria-valuemin="0" aria-valuemax="100">
                Correct: {{ $correctPercentage }}%
            </div>
            <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: {{ $incorrectPercentage }}%" 
                aria-valuenow="{{ $incorrectPercentage }}" aria-valuemin="0" aria-valuemax="100">
                Incorrect: {{ $incorrectPercentage }}%
            </div>
        </div>
    </div>
</div>