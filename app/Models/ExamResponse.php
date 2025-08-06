<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResponse extends Model
{
    protected $table = 'exam_responses';

    protected $fillable = ['examinee_id', 'question_id', 'response', 'is_correct'];
    
    protected $casts = [
        'is_correct' => 'boolean',
    ];
    public function examinee()
    {
        return $this->belongsTo(Examinee::class);
    }
    
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
