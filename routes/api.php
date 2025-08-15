<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeliveriesController;
use App\Http\Controllers\Api\DriverPingsController;
use App\Http\Controllers\Api\DeliveryStatusController;
use App\Http\Controllers\Api\ReportingController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Deliveries
        Route::post('deliveries/{delivery}/assign', [DeliveriesController::class, 'assign']);
        Route::post('deliveries/{delivery}/status', [DeliveryStatusController::class, 'update']);

        // Driver pings
        Route::post('drivers/{driver}/pings', [DriverPingsController::class, 'store']);

        // Reporting
        Route::post('reports/eod/{city}', [ReportingController::class, 'runEndOfDay']);
    });
});
