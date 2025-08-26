<?php

namespace App\Providers;

use App\Repositories\BitrixApiRepository;
use App\Services\Bitrix\BitrixCredentialResolver;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the repository with credential resolution
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the TaskObserver
        \App\Models\TaskModel::observe(\App\Observers\TaskObserver::class);

        // Share environment variables globally with all Blade views
        View::share('env', [
            'APP_ENV' => env('APP_ENV'),
            'APP_URL' => env('APP_URL'),
        ]);
    }
}
