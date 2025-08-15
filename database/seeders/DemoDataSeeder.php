<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    Role, User, City, Driver, Vehicle, Order,
    Delivery, Package, DeliveryStatusHistory
};
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Roles
        $roles = [
            'admin', 'city_manager', 'dispatcher',
            'driver', 'customer', 'readonly/auditor'
        ];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Cities
        $cpt = City::create(['name' => 'Cape Town']);
        $pretoria = City::create(['name' => 'Pretoria']);

        // 3. Users & Role Assignment
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);

        $admin->roles()->attach(Role::where('name', 'admin')->first());

        $manager = User::create([
            'name' => 'City Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password')
        ]);
        $manager->roles()->attach(Role::where('name', 'city_manager')->first());

        $dispatcher = User::create([
            'name' => 'Dispatcher',
            'email' => 'dispatcher@example.com',
            'password' => Hash::make('password')
        ]);
        $dispatcher->roles()->attach(Role::where('name', 'dispatcher')->first());

        $customer = User::create([
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => Hash::make('password')
        ]);
        $customer->roles()->attach(Role::where('name', 'customer')->first());

        $driverUser = User::create([
            'name' => 'John Driver',
            'email' => 'driver@example.com',
            'password' => Hash::make('password')
        ]);
        $driverUser->roles()->attach(Role::where('name', 'driver')->first());

        // 4. Vehicle & Driver
        $vehicle = Vehicle::create(['reg_no' => 'ABC-123']);
        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'license_no' => 'D1234567',
            'vehicle_id' => $vehicle->id,
            'status' => 'available',
            'current_load' => 0,
            'last_ping_at' => now(),
            'current_city_id' => $cpt->id
        ]);

        // 5. Orders
        $order = Order::create([
            'order_no' => 'ORD-001',
            'customer_id' => $customer->id,
            'pickup_address' => '123 Broadway, New York, NY',
            'dropoff_address' => '456 Madison Ave, New York, NY',
            'city_id' => $cpt->id,
            'status' => 'pending'
        ]);

        // 6. Delivery
        $delivery = Delivery::create([
            'order_id' => $order->id,
            'assigned_driver_id' => $driver->id,
            'status' => 'scheduled',
            'scheduled_at' => now()->addDay(),
            'city_id' => $cpt->id,
            'pickup_lat' => 40.7128,
            'pickup_lng' => -74.0060
        ]);

        // 7. Packages
        $package1 = Package::create([
            'delivery_id' => $delivery->id,
            'sku' => 'PKG-001',
            'weight' => 2.5,
            'dimensions' => '10x5x3',
            'status' => 'in_transit',
            'customer_id' => $customer->id,
            'city_id' => $cpt->id
        ]);
    }
}
