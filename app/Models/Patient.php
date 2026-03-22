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

    // ── EXISTING RELATIONSHIPS ──────────────────────────────
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ── NEW FINANCIAL RELATIONSHIPS ─────────────────────────
    public function billingEntries()
    {
        return $this->hasMany(PatientBillingEntry::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /** Total outstanding (unpaid) amount */
    public function totalDue(): float
    {
        return $this->billingEntries()
            ->where('is_paid', false)
            ->where('is_past_note', false)
            ->sum('amount');
    }

    /** Total paid amount */
    public function totalPaid(): float
    {
        return $this->billingEntries()
            ->where('is_paid', true)
            ->sum('amount');
    }
}