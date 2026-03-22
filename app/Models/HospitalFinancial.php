<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalFinancial extends Model
{
    public $guarded = [];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}