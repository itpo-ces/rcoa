<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\ExamResult;

class ExamResultsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ExamResult::with(['examinee.unit', 'exam'])
            ->orderBy('id', 'asc');

        // Apply filters if provided
        if (!empty($this->filters)) {
            if ($this->filters['examFilter'] != 'all') {
                $query->where('exam_id', $this->filters['examFilter']);
            }

            if ($this->filters['nameFilter'] != 'all') {
                $query->whereHas('examinee', function($q) {
                    $q->where('id', $this->filters['nameFilter']);
                });
            }

            if ($this->filters['designationFilter'] != 'all') {
                $query->whereHas('examinee', function($q) {
                    $q->where('designation', $this->filters['designationFilter']);
                });
            }

            if ($this->filters['unitFilter'] != 'all') {
                $query->whereHas('examinee', function($q) {
                    $q->where('unit', $this->filters['unitFilter']);
                });
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            // 'Examinee ID',
            '#',
            'Examination',
            'Name',
            'Designation',
            'Unit',
            'Total Questions',
            'Score',
            'Percentage',
            'Rating'
        ];
    }

    public function map($result): array
    {
        return [
            // $result->examinee->id,
            $result->id,
            $result->exam->title ?? 'N/A',
            $result->examinee->full_name,
            $result->examinee->designation,
            $result->examinee->unit_description,
            $result->total_questions,
            $result->score,
            $result->percentage . '%',
            $result->rating
        ];
    }
}