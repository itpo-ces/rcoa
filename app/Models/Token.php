<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';

    protected $fillable = ['token', 'is_used', 'used_at', 'status'];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];
    
    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_USED = 'used';
    
    public function examinee()
    {
        return $this->hasOne(Examinee::class);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'available'   => 'Available',
            'in_use'      => 'In Use',
            'used'       => 'Used',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public static function getEnumValues($column)
    {
        $type = \DB::select("SHOW COLUMNS FROM " . (new self)->getTable() . " WHERE Field = '{$column}'")[0]->Type;
    
        preg_match('/^enum\((.*)\)$/', $type, $matches);
    
        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
            $enum[] = [
                'id' => $v,
                'description' => ucwords(strtoupper(str_replace('_', ' ', $v)))
            ];
        }
    
        return $enum;
    }
}