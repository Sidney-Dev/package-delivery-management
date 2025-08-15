<?php

use App\Actions\Contracts\Action;
use App\Actions\Deliveries\AssignDeliveryToDriverAction;
use App\Actions\Deliveries\UpdateDeliveryStatusAction;
use App\Actions\Drivers\RecordDriverLocationPingAction;
use App\Actions\Reporting\MarkEndOfDayReturnsAction;

it('action classes are invokable and implement contract', function () {
    $actions = [
        new AssignDeliveryToDriverAction(),
        new UpdateDeliveryStatusAction(),
        new RecordDriverLocationPingAction(),
        new MarkEndOfDayReturnsAction(),
    ];

    foreach ($actions as $a) {
        expect($a)->toBeInstanceOf(Action::class);
        expect(method_exists($a, '__invoke'))->toBeTrue();
    }
});

it('assigns driver with minimal load', function () {
    $delivery = ['id' => 1, 'city_id' => 10, 'pickup_lat' => -23.5, 'pickup_lng' => -46.6];
    $drivers = [
        ['id' => 1, 'city_id' => 10, 'is_active' => true, 'is_available' => true, 'current_load' => 5, 'lat' => -23.6, 'lng' => -46.7],
        ['id' => 2, 'city_id' => 10, 'is_active' => true, 'is_available' => true, 'current_load' => 1, 'lat' => -23.8, 'lng' => -46.9],
        ['id' => 3, 'city_id' => 11, 'is_active' => true, 'is_available' => true, 'current_load' => 0],
    ];

    $action = new AssignDeliveryToDriverAction();
    $result = $action($delivery, $drivers);

    expect($result['assigned_driver_id'])->toBe(2);
});

it('updates delivery status and sets intents', function () {
    $delivery = ['id' => 10, 'status' => 'pending'];
    $action = new UpdateDeliveryStatusAction();
    $result = $action($delivery, 'delivered', ['proof' => ['type' => 'signature']]);

    expect($result['ok'])->toBeTrue()
        ->and($result['delivery']['status'])->toBe('delivered')
        ->and($result['intents']['notify_customer'])->toBeTrue();
});

it('records driver ping and validates coordinates', function () {
    $driver = ['id' => 42];
    $ping = ['lat' => 10.0, 'lng' => 20.0, 'heading' => 90, 'speed' => 30];
    $action = new RecordDriverLocationPingAction();

    $ok = $action($driver, $ping);
    expect($ok['ok'])->toBeTrue();

    $bad = $action($driver, ['lat' => 999, 'lng' => 20]);
    expect($bad['ok'])->toBeFalse();
});

it('marks EOD returns for undelivered packages', function () {
    $packages = [
        ['id' => 1, 'city_id' => 5, 'status' => 'pending'],
        ['id' => 2, 'city_id' => 5, 'status' => 'delivered'],
        ['id' => 3, 'city_id' => 6, 'status' => 'pending'],
    ];
    $ctx = ['city_id' => 5, 'eod_time' => '18:00'];

    $action = new MarkEndOfDayReturnsAction();
    $result = $action($packages, $ctx);

    $updated = $result['updated_packages'];
    expect(collect($updated)->firstWhere('id', 1)['status'])->toBe('return')
        ->and(collect($updated)->firstWhere('id', 2)['status'])->toBe('delivered')
        ->and(collect($updated)->firstWhere('id', 3)['status'])->toBe('pending')
        ->and($result['stats']['returned'])->toBe(1);
});
