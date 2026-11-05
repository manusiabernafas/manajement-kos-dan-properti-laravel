<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        Blade::component('sidebar', \App\View\Components\Sidebar::class);
        Blade::component('navbar', \App\View\Components\Navbar::class);

        RedirectIfAuthenticated::redirectUsing(function () {
            $user = Auth::user();

            return $user && $user->hasRole('Tenant') ? route('tenant.dashboard') : route('dashboard');
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
