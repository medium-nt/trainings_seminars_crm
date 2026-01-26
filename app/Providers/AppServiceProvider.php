<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();

        Gate::define('is-admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('is-client', function (User $user) {
            return $user->isClient();
        });

        Gate::define('is-manager', function (User $user) {
            return $user->isManager();
        });

        Gate::define('is-teacher', function (User $user) {
            return $user->isTeacher();
        });

        Gate::define('is-admin-or-manager', function (User $user) {
            return $user->isAdmin() || $user->isManager();
        });

        Gate::define('is-admin-or-manager-or-teacher', function (User $user) {
            return $user->isAdmin() || $user->isManager() || $user->isTeacher();
        });

    }
}
