<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examinee extends Model
{
    protected $table = 'examinees';

    protected $fillable = [
        'token_id', 'rank', 'first_name', 'middle_name', 'last_name',
        'qualifier', 'designation', 'unit', 'subunit', 'station',
        'accepted_privacy'
    ];

    protected $casts = [
        'accepted_privacy' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
    
    public function token()
    {
        return $this->belongsTo(Token::class);
    }
    
    public function responses()
    {
        return $this->hasMany(ExamResponse::class);
    }
}
