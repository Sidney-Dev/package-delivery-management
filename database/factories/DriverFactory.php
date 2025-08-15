<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'license_no' => strtoupper($this->faker->bothify('??######')),
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'status' => 'active',
            'current_load' => $this->faker->numberBetween(0, 10),
            'last_ping_at' => now(),
            'current_city_id' => \App\Models\City::factory(),
        ];
    }
}
