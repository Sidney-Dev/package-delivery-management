<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'assigned_driver_id',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'city_id',
        'pickup_lat',
        'pickup_lng'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function driver(): BelongsTo {
        return $this->belongsTo(Driver::class, 'assigned_driver_id');
    }
    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }

    public function packages(): HasMany {
        return $this->hasMany(Package::class);
    }
    public function histories(): HasMany {
        return $this->hasMany(DeliveryStatusHistory::class);
    }
}
