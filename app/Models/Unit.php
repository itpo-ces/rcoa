<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'ref_units';

    public function subunits(){
        return $this->hasMany(Subunit::class, 'UnitId', 'SubUnitId');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'UnitId', 'unit_id');
    }

}
