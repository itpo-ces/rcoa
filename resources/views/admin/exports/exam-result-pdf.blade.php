<!DOCTYPE html>
<html>
<head>
    <title>Exam Result - {{ $result->examinee->full_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #2c3e50; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px; border: 1px solid #ddd; }
        .info-table tr:nth-child(even) { background-color: #f2f2f2; }
        .questions-table { width: 100%; border-collapse: collapse; }
        .questions-table th, .questions-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .questions-table th { background-color: #2c3e50; color: white; }
        .correct { color: green; }
        .incorrect { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Exam Result</h1>
        <p>Generated on: {{ now()->format('F j, Y H:i:s') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Examinee ID:</strong></td>
            <td>{{ $result->examinee->id }}</td>
        </tr>
        <tr>
            <td><strong>Name:</strong></td>
            <td>{{ $result->examinee->full_name }}</td>
        </tr>
        <tr>
            <td><strong>Exam:</strong></td>
            <td>{{ $result->exam->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Designation:</strong></td>
            <td>{{ $result->examinee->designation }}</td>
        </tr>
        <tr>
            <td><strong>Unit:</strong></td>
            <td>{{ $result->examinee->unit_description ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Total Questions:</strong></td>
            <td>{{ $result->total_questions }}</td>
        </tr>
        <tr>
            <td><strong>Score:</strong></td>
            <td>{{ $result->score }}</td>
        </tr>
        <tr>
            <td><strong>Percentage:</strong></td>
            <td>{{ $result->percentage }}%</td>
        </tr>
        <tr>
            <td><strong>Rating:</strong></td>
            <td>{{ $result->rating }}</td>
        </tr>
    </table>

    <h2>Question Details</h2>
    <table class="questions-table">
        <thead>
            <tr>
                <th>Question</th>
                <th>Response</th>
                <th>Correct Answer</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result->examinee->responses as $response)
            <tr>
                <td>{{ $response->question->question_text }}</td>
                <td>{{ $response->response }}</td>
                <td>{{ $response->question->correct_answer }}</td>
                <td class="{{ $response->is_correct ? 'correct' : 'incorrect' }}">
                    {{ $response->is_correct ? 'Correct' : 'Incorrect' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>