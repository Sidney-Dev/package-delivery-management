<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with(['city', 'customer'])
            ->latest('id')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = Order::create($request->validated());
        
        return response()->json([
            'success'=>true,
            'data'=> $order
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $order->load('city', 'customer')
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order->update($request->validated());
        
        return response()->json([
            'success'=> true,
            'data'=> $order
        ]);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        
        return response()->json([
            'success'=> true,
            'message'=> 'Order deleted'
        ]);
    }
}
