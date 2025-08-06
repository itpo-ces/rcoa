<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    
    protected $fillable = ['exam_id', 'question_text', 'type', 'difficulty', 'correct_answer', 'options'];
    
    protected $casts = [
        'options' => 'array'
    ];
    
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
