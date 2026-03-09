<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    
    public function register(): void
    {
        Gate::define('super_admin', function ($user) {
            return $user->role === 'super_admin';
        });

        Gate::define('hospital_admin', function ($user) {
            return $user->role === 'hospital_admin';
        });

        Gate::define('doctor', function ($user) {
            return $user->role === 'doctor';
        });

        // Optional: combined gate
        Gate::define('admin_or_doctor', function ($user) {
            return in_array($user->role, [
                'super_admin',
                'hospital_admin',
                'doctor'
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
