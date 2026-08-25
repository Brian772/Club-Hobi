<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
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
        View::composer('layouts.partials.notification-panel', function ($view) {
            $view->with('notifications', auth()->check()
                ? auth()->user()->notifications()->take(5)->get()
                : collect());
        });

        Gate::define('admin', function($user) {
            return $user->role_global === 'admin';
        });
    }
}
