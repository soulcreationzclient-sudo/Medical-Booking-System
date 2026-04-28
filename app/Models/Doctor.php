<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public $guarded = [];

    public function specializations()
    {
        return $this->belongsToMany(
            \App\Models\Specialization::class,
            'doctor_specializations',
            'doctor_id',
            'specialization_id'
        );
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}