<?php

namespace App\Http\Controllers\Api;

use App\Actions\Deliveries\AssignDeliveryToDriverAction;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Driver;
use Illuminate\Http\Request;

class DeliveriesController extends Controller
{
    public function assign(Request $request, Delivery $delivery, AssignDeliveryToDriverAction $assign)
    {
        // In a real implementation, we'd fetch eligible drivers from DB. For now, accept a payload or fallback to simple query.
        $driversPayload = $request->input('drivers');
        if (!is_array($driversPayload)) {
            $driversPayload = Driver::query()
                ->where('current_city_id', $delivery->city_id)
                ->where('status', 'active')
                ->limit(50)
                ->get(['id as id', 'current_city_id as city_id', 'current_load'])
                ->map(fn($d) => $d->toArray() + ['is_active' => true, 'is_available' => true])
                ->all();
        }

        $result = $assign([
            'id' => $delivery->id,
            'city_id' => $delivery->city_id,
            'package_count' => $delivery->packages()->count(),
            'pickup_lat' => $delivery->pickup_lat,
            'pickup_lng' => $delivery->pickup_lng,
        ], $driversPayload);

        if ($result['assigned_driver_id'] ?? null) {
            $delivery->assigned_driver_id = $result['assigned_driver_id'];
            $delivery->status = 'assigned';
            $delivery->save();
        }

        return response()->json(['delivery' => $delivery, 'assignment' => $result]);
    }
}
