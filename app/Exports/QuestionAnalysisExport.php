<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Question;
use App\Models\ExamResponse;

class QuestionAnalysisExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Question::with(['exam'])
            ->select('questions.*')
            ->addSelect([
                'total_responses' => ExamResponse::selectRaw('COUNT(*)')
                    ->whereColumn('question_id', 'questions.id'),
                'correct_responses' => ExamResponse::selectRaw('SUM(is_correct)')
                    ->whereColumn('question_id', 'questions.id')
            ]);

        // Apply filters
        if (!empty($this->filters)) {
            if ($this->filters['examFilter'] != 'all') {
                $query->where('exam_id', $this->filters['examFilter']);
            }

            if ($this->filters['typeFilter'] != 'all') {
                $query->where('type', $this->filters['typeFilter']);
            }

            if ($this->filters['difficultyFilter'] != 'all') {
                $query->where('difficulty', $this->filters['difficultyFilter']);
            }
        }

        return $query->orderBy('id', 'asc');
    }

    public function headings(): array
    {
        return [
            '#',
            'Exam Title',
            'Question',
            'Type',
            'Difficulty',
            'Correct Answer',
            'Total Responses',
            'Correct Responses',
            'Correct Percentage',
            'Success Rate'
        ];
    }

    public function map($question): array
    {
        $totalResponses = $question->total_responses ?? 0;
        $correctResponses = $question->correct_responses ?? 0;
        $correctPercentage = $totalResponses > 0 ? round(($correctResponses / $totalResponses) * 100) : 0;

        return [
            $question->id,
            $question->exam->title ?? 'N/A',
            $question->question_text,
            $question->type_label,
            $question->difficulty_label,
            $question->correct_answer,
            $totalResponses,
            $correctResponses,
            $correctPercentage . '%',
            $this->getSuccessRateText($correctPercentage)
        ];
    }

    private function getSuccessRateText($correctPercentage)
    {
        if ($correctPercentage >= 80) return 'Excellent';
        if ($correctPercentage >= 60) return 'Good';
        if ($correctPercentage >= 40) return 'Average';
        if ($correctPercentage >= 20) return 'Poor';
        return 'Very Poor';
    }
}
