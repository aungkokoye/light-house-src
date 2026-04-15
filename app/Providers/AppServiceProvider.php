<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\StaffPosition;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SitePolicy;
use App\Policies\StaffPositionPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Site::class, SitePolicy::class);
        Gate::policy(StaffPosition::class, StaffPositionPolicy::class);

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url('/reset-password?token=' . $token . '&email=' . urlencode($user->email));
        });
    }
}
