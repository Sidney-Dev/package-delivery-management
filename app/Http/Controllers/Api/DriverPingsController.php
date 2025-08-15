<?php

namespace App\Http\Controllers\Api;

use App\Actions\Drivers\RecordDriverLocationPingAction;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverPingsController extends Controller
{
    public function store(Request $request, Driver $driver, RecordDriverLocationPingAction $record)
    {
        $data = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'heading' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'occurred_at' => 'nullable|date',
        ]);

        $result = $record(['id' => $driver->id], $data);

        // Persist minimal ping if ok
        if ($result['ok'] ?? false) {
            $driver->last_ping_at = now();
            $driver->save();
        }

        return response()->json($result, ($result['ok'] ?? false) ? 201 : 422);
    }
}
