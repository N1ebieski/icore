<?php

namespace N1ebieski\ICore\Providers;

use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \N1ebieski\ICore\Models\User::class => \N1ebieski\ICore\Policies\UserPolicy::class,
        \N1ebieski\ICore\Models\Token\PersonalAccessToken::class => \N1ebieski\ICore\Policies\TokenPolicy::class,
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
            return ($user->hasRole(Name::SUPER_ADMIN)
                & !strpos($ability, 'Self')
                & !strpos($ability, 'Default')) ? true : null;
        });

        Sanctum::usePersonalAccessTokenModel(\N1ebieski\ICore\Models\Token\PersonalAccessToken::class);
        Sanctum::authenticateAccessTokensUsing(function ($token, $isValid) {
            return $isValid
                && ($token->expired_at === null || $token->expired_at->gte(Carbon::now()))
                && (
                    (Route::currentRouteName() === Config::get('sanctum.refresh_route_name')) ?
                    $token->can('refresh')
                    : $token->cant('refresh')
                );
        });
    }
}
