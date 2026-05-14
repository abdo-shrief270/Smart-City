<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'firebase_key',
        'plate_number',
        'gate_number',
        'direction',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'gate_number' => 'integer',
    ];
}
