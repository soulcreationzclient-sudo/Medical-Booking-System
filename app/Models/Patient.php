<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'phone_no',
        'age',
        'gender',
        'ic_passport_no',
        'dob',
        'blood_type',
        'marital_status',
        'nationality',
        'address',
        'state',
        'city',
        'postcode',
        'country',
        'emergency_contact_name',
        'emergency_contact_no',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}