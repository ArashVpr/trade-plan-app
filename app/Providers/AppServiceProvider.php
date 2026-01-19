<?php

namespace App\Providers;

use App\Models\AnalysisTracker;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        // Only force login in local environment
        //  if ($this->app->environment('local')) {
        //     $user = User::find(1);
        //     if ($user) {
        //         Auth::login($user);
        //     }
        // }
    }
}
