<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTreatment extends Model
{
    protected $fillable = [
        'booking_id',
        'treatment_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'unit_price'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}