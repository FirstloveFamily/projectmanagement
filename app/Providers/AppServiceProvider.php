<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::before(function (?User $user) {
            return $user?->isAdmin() ? true : null;
        });

        Gate::define('view-dashboard', fn (?User $user) => true);
        Gate::define('view-projects', fn (?User $user) => true);
        Gate::define('view-companies', fn (?User $user) => true);
        Gate::define('view-reports', fn (?User $user) => true);

        Gate::define('manage-users', fn (?User $user) => $user?->isAdmin() ?? false);
        Gate::define('manage-companies', fn (?User $user) => $user?->isAdmin() || $user?->isItDev());
        Gate::define('manage-projects', fn (?User $user) => $user?->isAdmin() || $user?->isItDev());
        Gate::define('manage-tasks', fn (?User $user) => $user?->isAdmin() || $user?->isItDev());
        Gate::define('manage-trainings', fn (?User $user) => $user?->isAdmin() || $user?->isItDev());
        Gate::define('manage-requests', fn (?User $user) => $user?->isAdmin() || $user?->isItDev());
        Gate::define('manage-settings', fn (?User $user) => $user?->isAdmin() ?? false);
    }
}
