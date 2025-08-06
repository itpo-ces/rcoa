<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subunit extends Model
{
    use HasFactory;

    protected $table = 'ref_subunits';

    public function unit(){
        return $this->belongsTo(Unit::class, 'UnitId', 'UnitId');
    }

    public function station(){
        return $this->hasMany(Station::class, 'SubUnitId', 'SubUnitId');
    }

    
}
