<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function orders(): HasMany { 
        return $this->hasMany(Order::class); 
    }
    public function drivers(): HasMany { 
        return $this->hasMany(Driver::class, 'current_city_id'); 
    }

    public function deliveries(): HasMany { 
        return $this->hasMany(Delivery::class); 
    }

    public function packages(): HasMany { 
        return $this->hasMany(Package::class); 
    }
}
