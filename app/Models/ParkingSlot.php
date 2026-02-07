<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'area',
        'slot_number',
        'status',
        'cost_per_hour',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(ParkingReservation::class);
    }

    public function activeReservation()
    {
        return $this->hasOne(ParkingReservation::class)->where('status', 'active')->latest();
    }
}
