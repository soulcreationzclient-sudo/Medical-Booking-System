<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    public $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /** Total cost of all medicines in this prescription */
    public function totalCost(): float
    {
        return $this->items->sum(fn($item) => $item->price_at_time * $item->quantity);
    }
}