<?php

namespace App\Providers;

use App\Http\Middleware\ValidateApiToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CustomAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Register the token driver
        Auth::extend('token', function ($app, $name, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\Guard...
            return new \App\Services\Auth\TokenGuard(
                Auth::createUserProvider($config['provider']),
                $app->make('request')
            );
        });

        // Register the middleware
        Route::aliasMiddleware('auth.api.token', ValidateApiToken::class);
    }
}
