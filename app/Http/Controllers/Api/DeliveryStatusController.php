<?php

namespace App\Http\Controllers\Api;

use App\Actions\Deliveries\UpdateDeliveryStatusAction;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryStatusController extends Controller
{
    public function update(Request $request, Delivery $delivery, UpdateDeliveryStatusAction $update)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'proof' => 'nullable',
        ]);

        $result = $update(['id' => $delivery->id, 'status' => $delivery->status], $data['status'], $data);

        if (($result['ok'] ?? false) && isset($result['delivery']['status'])) {
            $delivery->status = $result['delivery']['status'];
            if ($delivery->status === 'delivered') {
                $delivery->completed_at = now();
            }
            $delivery->save();
        }

        return response()->json($result);
    }
}
