<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examinee extends Model
{
    protected $table = 'examinees';

    protected $fillable = [
        'token_id',
        'rank', 
        'first_name',
        'middle_name',
        'last_name',
        'qualifier',
        'designation',
        'unit',
        'subunit',
        'station',
        'last_question_number',
        'exam_progress',
        'accepted_privacy',
        'accepted_certification'
    ];

    protected $casts = [
        'accepted_privacy' => 'boolean',
        'accepted_certification' => 'boolean',
        'exam_progress' => 'array'
    ];

    public function getFullNameAttribute()
    {
        return strtoupper(trim("{$this->rank} {$this->first_name} {$this->middle_name} {$this->last_name}"));
    }
    
    public function token()
    {
        return $this->belongsTo(Token::class);
    }
    
    public function responses()
    {
        return $this->hasMany(ExamResponse::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit','UnitId');
    }

    public function getUnitDescriptionAttribute()
    {
        $unitId = (int) $this->unit;
        return Unit::find($unitId)->OrderNumberPrefix ?? 'N/A';
    }

}
