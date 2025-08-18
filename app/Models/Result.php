<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'examinee_id',
        'exam_id',
        'score',
        'total_questions',
        'percentage',
        'rating',
        'status',
    ];

    protected $casts = [
        'score' => 'integer',
        'total_questions' => 'integer',
        'percentage' => 'decimal:2',
    ];

    /**
     * Get the examinee associated with the result.
     */

    public function examinee()
    {
        return $this->belongsTo(Examinee::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
