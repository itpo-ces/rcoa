<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';

    protected $fillable = [
        'title',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'number_of_questions',
        'is_active',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'duration_minutes' => 'integer',
        'number_of_questions' => 'integer',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'is_active' => 'boolean',
    ];
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
