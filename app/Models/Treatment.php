<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = [
        'hospital_id',
        'name',
        'code',
        'category',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function bookingTreatments()
    {
        return $this->hasMany(BookingTreatment::class);
    }

    public function billingEntries()
    {
        return $this->hasMany(PatientBillingEntry::class);
    }
}