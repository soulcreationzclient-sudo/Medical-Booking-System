<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    //
    public $guarded = [];
    
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }
}
