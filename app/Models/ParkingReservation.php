<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parking_slot_id',
        'start_time',
        'end_time',
        'total_cost',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parkingSlot(): BelongsTo
    {
        return $this->belongsTo(ParkingSlot::class);
    }
}
