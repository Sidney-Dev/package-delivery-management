<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_no',
    ];

    /**
     * Get the driver assigned to the vehicle
     */
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }
}
