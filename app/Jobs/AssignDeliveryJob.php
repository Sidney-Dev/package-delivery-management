<?php

namespace App\Jobs;

use App\Actions\Deliveries\AssignDeliveryToDriverAction;
use App\Models\Delivery;
use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $deliveryId)
    {
    }

    public function handle(AssignDeliveryToDriverAction $assign): void
    {
        $delivery = Delivery::withCount('packages')->find($this->deliveryId);
        if (!$delivery) {
            return;
        }

        $drivers = Driver::query()
            ->where('current_city_id', $delivery->city_id)
            ->where('status', 'active')
            ->limit(100)
            ->get(['id as id', 'current_city_id as city_id', 'current_load'])
            ->map(fn($d) => $d->toArray() + ['is_active' => true, 'is_available' => true])
            ->all();

        $result = $assign([
            'id' => $delivery->id,
            'city_id' => $delivery->city_id,
            'package_count' => $delivery->packages_count ?? 0,
            'pickup_lat' => $delivery->pickup_lat,
            'pickup_lng' => $delivery->pickup_lng,
        ], $drivers);

        if ($result['assigned_driver_id'] ?? null) {
            $delivery->assigned_driver_id = $result['assigned_driver_id'];
            $delivery->status = 'assigned';
            $delivery->save();
        }
    }
}
