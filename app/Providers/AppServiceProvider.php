<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Services\SettingService;
use App\View\Composers\TopbarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(
            SettingService::class,
            fn() => new SettingService()
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        RateLimiter::for('notifications', function (Request $request) {
            return Limit::perMinute(10)
                ->by(
                    $request->user()?->id
                    ?? $request->ip()
                );
        });

        RateLimiter::for('exports', function (Request $request) {
            return Limit::perMinute(20)
                ->by(
                    $request->user()?->id
                    ?? $request->ip()
                );
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin')
                ? true
                : null;
        });

        View::composer(
            'components.layout.topbar',
            TopbarComposer::class
        );
    }
}
