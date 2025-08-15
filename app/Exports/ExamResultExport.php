<?php

namespace App\Exports;

use App\Models\ExamResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamResultExport implements FromCollection, WithHeadings, WithStyles
{
    protected $result;

    public function __construct(ExamResult $result)
    {
        $this->result = $result;
    }

    public function collection()
    {
        $data = [
            ['Examinee ID', $this->result->examinee->id],
            ['Name', $this->result->examinee->full_name],
            ['Exam', $this->result->exam->title ?? 'N/A'],
            ['Designation', $this->result->examinee->designation],
            ['Unit', $this->result->examinee->unit_description ?? 'N/A'],
            ['Total Questions', $this->result->total_questions],
            ['Score', $this->result->score],
            ['Percentage', $this->result->percentage . '%'],
            ['Rating', $this->result->rating],
            ['', ''],
            ['Question', 'Response', 'Correct Answer', 'Result']
        ];

        foreach ($this->result->examinee->responses as $response) {
            $result = $response->is_correct ? 'Correct' : 'Incorrect';
            $data[] = [
                $response->question->question_text,
                $response->response,
                $response->question->correct_answer,
                $result
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
        ];
    }
}