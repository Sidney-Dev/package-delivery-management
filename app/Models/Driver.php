<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_no',
        'vehicle_id',
        'status',
        'current_load',
        'last_ping_at',
        'current_city_id'
    ];

    protected $casts = [
        'last_ping_at' => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }
    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'current_city_id');
    }

    public function deliveries(): HasMany {
        return $this->hasMany(Delivery::class, 'assigned_driver_id');
    }
}
