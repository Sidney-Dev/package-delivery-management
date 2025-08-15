<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\City;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'customer_id',
        'pickup_address',
        'dropoff_address',
        'city_id',
        'status'
    ];

    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(User::class);
    }

}
