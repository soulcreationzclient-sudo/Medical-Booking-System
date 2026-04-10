<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientBillingEntry extends Model
{
    public $guarded = [];

    protected $casts = [
        'amount'       => 'decimal:2',
        'is_paid'      => 'boolean',
        'is_past_note' => 'boolean',
        'paid_at'      => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}