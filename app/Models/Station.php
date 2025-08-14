<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $table = 'ref_stations';

    public function subunit(){
        return $this->belongsTo(Subunit::class, 'SubUnitId', 'SubUnitId');
    }

    // public function substation(){
    //     return $this->hasMany(Substation::class, 'StationId', 'StationId');
    // }
}
