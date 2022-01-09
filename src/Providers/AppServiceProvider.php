<?php

namespace N1ebieski\ICore\Providers;

use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\GuzzleHttp\ClientInterface::class, \GuzzleHttp\Client::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(\N1ebieski\ICore\Models\PersonalAccessToken::class);
        Sanctum::authenticateAccessTokensUsing(function ($token, $isValid) {
            return $isValid
                && $token->expired_at->gte(Carbon::now())
                && (
                    (Route::currentRouteName() === Config::get('sanctum.refresh_route_name')) ?
                    $token->can('refresh')
                    : $token->cant('refresh')
                );
        });
    }
}
