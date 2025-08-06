<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';

    protected $fillable = ['token', 'is_used', 'used_at'];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];
    
    public function examinee()
    {
        return $this->hasOne(Examinee::class);
    }
}
