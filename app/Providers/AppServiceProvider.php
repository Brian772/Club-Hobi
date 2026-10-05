<?php

namespace App\Providers;

use App\Models\Club;
use App\Models\Hobby;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

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
        if (app()->environment(['local', 'testing'])) {
            try {
                if (!Schema::hasTable('users')) {
                    Artisan::call('migrate', ['--force' => true]);
                }

                $shouldSeedDefaultData = Schema::hasTable('users') && User::count() === 0
                    || Schema::hasTable('hobbies') && Hobby::count() === 0
                    || Schema::hasTable('clubs') && Club::count() === 0;

                if ($shouldSeedDefaultData) {
                    Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (\Throwable $e) {
                logger()->warning('Default data restore guard failed: ' . $e->getMessage());
            }
        }

        View::composer(['layouts.partials.notification-panel', 'layouts.partials.topbar'], function ($view) {
            $notifications = auth()->check()
                ? auth()->user()->notifications()->latest('created_at')->take(5)->get()
                : collect();

            $view->with('notifications', $notifications)
                ->with('unreadNotificationsCount', auth()->check()
                    ? auth()->user()->notifications()->where('is_read', false)->count()
                    : 0);
        });

        Gate::define('admin', function ($user) {
            return $user->role_global === 'admin';
        });

        Relation::enforceMorphMap([
            'user' => User::class,
            'post' => Post::class,
            'comment' => Comment::class,
        ]);
    }
}
