<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'sku',
        'weight',
        'dimensions',
        'status',
        'return_reason',
        'customer_id',
        'city_id'
    ];

    public function delivery(): BelongsTo {
        return $this->belongsTo(Delivery::class);
    }
}
