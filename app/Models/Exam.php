<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';

    protected $fillable = ['title', 'exam_date', 'duration_minutes', 'number_of_questions'];

    protected $casts = [
        'exam_date' => 'date',
        'duration_minutes' => 'integer',
        'number_of_questions' => 'integer',
    ];
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
