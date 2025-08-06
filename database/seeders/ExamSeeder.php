<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam::create([
            'title' => 'Radio Communication Exam',
            'exam_date' => '2025-08-10',
            'duration_minutes' => 30,
            'number_of_questions' => 30,
        ]);
    }
}
