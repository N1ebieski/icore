<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * [AuthServiceProvider description]
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \N1ebieski\ICore\Models\User::class => \N1ebieski\ICore\Policies\UserPolicy::class,
        \N1ebieski\ICore\Models\Socialite::class => \N1ebieski\ICore\Policies\SocialitePolicy::class,
        \N1ebieski\ICore\Models\Comment\Comment::class => \N1ebieski\ICore\Policies\CommentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('super-admin')
                & !strpos($ability, 'Self')
                & !strpos($ability, 'Default')) ? true : null;
        });
    }
}
