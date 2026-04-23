<?php

namespace App\Providers;

use App\Events\AuditableActionPerformed;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Listeners\DispatchAuditLog;
use App\Models\Site;
use App\Models\StaffPosition;
use App\Models\StaffRole;
use App\Models\ChatKnowledge;
use App\Policies\ChatKnowledgePolicy;
use App\Policies\ChatPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SitePolicy;
use App\Policies\StaffPositionPolicy;
use App\Policies\StaffRolePolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
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
        Event::listen(UserLoggedIn::class, DispatchAuditLog::class);
        Event::listen(UserLoggedOut::class, DispatchAuditLog::class);
        Event::listen(AuditableActionPerformed::class, DispatchAuditLog::class);

        Gate::define('chat.stream', [ChatPolicy::class, 'stream']);
        Gate::policy(ChatKnowledge::class, ChatKnowledgePolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Site::class, SitePolicy::class);
        Gate::policy(StaffPosition::class, StaffPositionPolicy::class);
        Gate::policy(StaffRole::class, StaffRolePolicy::class);

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url('/reset-password?token=' . $token . '&email=' . urlencode($user->email));
        });
    }
}
