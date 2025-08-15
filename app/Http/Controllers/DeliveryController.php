<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;

/**
 * @todo remove $e->getMessage() in production
 */

class DeliveryController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $deliveries = Delivery::with('order', 'driver', 'city', 'packages')->get();

            return response()->json([
                'success' => true,
                'data'    => $deliveries
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deliveries.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreDeliveryRequest $request): JsonResponse
    {
        try {
            $delivery = Delivery::create($request->validated());

            return response()->json([
                'success' => true,
                'data'    => $delivery
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create delivery.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show(?Delivery $delivery): JsonResponse
    {
        try {
            if (!$delivery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $delivery->load('order', 'driver', 'city', 'packages')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the delivery.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateDeliveryRequest $request, Delivery $delivery): JsonResponse
    {
        try {
            $delivery->update($request->validated());

            return response()->json([
                'success' => true,
                'data'    => $delivery
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Delivery $delivery): JsonResponse
    {
        try {
            $delivery->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delivery deleted successfully.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete delivery.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
