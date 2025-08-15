<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\\Models\\Model' => 'App\\Policies\\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // If Passport migrations are not published in this repo, ignore them to avoid runtime errors
        // when running tests without the package migrations present.
        if (class_exists(Passport::class)) {
            // Uncomment the following line if you don't have Passport migrations published in this project
            // Passport::ignoreMigrations();

            Passport::tokensCan([
                'driver' => 'Access driver endpoints',
                'dispatcher' => 'Access dispatcher endpoints',
                'admin' => 'Full access',
            ]);

            Passport::enablePasswordGrant();
        }
    }
}
