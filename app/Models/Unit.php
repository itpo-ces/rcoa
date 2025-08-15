<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'ref_units';
    protected $primaryKey = 'UnitId'; // <-- Add this line
    public $incrementing = false;     // <-- If UnitId is not auto-increment
    protected $keyType = 'int';       // <-- Or 'string' if it's not an int

    public function subunits(){
        return $this->hasMany(Subunit::class, 'UnitId', 'SubUnitId');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'UnitId', 'unit_id');
    }

}
