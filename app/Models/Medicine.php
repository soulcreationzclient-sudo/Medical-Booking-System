<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'hospital_id',
        'name',
        'unit',
        'price',
        'stock',
        'description',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}