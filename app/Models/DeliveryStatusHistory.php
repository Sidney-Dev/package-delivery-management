<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'status',
        'actor_id',
        'note',
        'lat',
        'lng'
    ];

    public function delivery(): BelongsTo {
        return $this->belongsTo(Delivery::class);
    }
}
