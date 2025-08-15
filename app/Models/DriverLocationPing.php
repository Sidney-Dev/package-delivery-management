<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverLocationPing extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'lat',
        'lng',
        'heading',
        'speed',
        'occurred_at'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function driver(): BelongsTo {
        return $this->belongsTo(Driver::class);
    }
}
