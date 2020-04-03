<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [AppServiceProvider description]
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('recaptcha_v2', \N1ebieski\ICore\Rules\RecaptchaV2Rule::class);
        $this->app['validator']->extend('alpha_num_spaces', \N1ebieski\ICore\Rules\AlphaNumSpacesRule::class);

        $this->app['validator']->extend('not_present', function ($attribute, $value, $parameters) {
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Helpers\View', function ($app) {
            return $this->app->make(\N1ebieski\ICore\Helpers\ViewHelper::class);
        });

        $this->app->singleton('Helpers\Valid', function ($app) {
            return $this->app->make(\N1ebieski\ICore\Helpers\ValidHelper::class);
        });

        $this->app->singleton('Helpers\Active', function ($app) {
            return $this->app->make(\N1ebieski\ICore\Helpers\ActiveHelper::class);
        });

        $this->app->bind(\GuzzleHttp\Client::class, function ($app) {
            return new \GuzzleHttp\Client(['timeout' => 10.0]);
        });

        $this->app->bindMethod(
            \N1ebieski\ICore\Jobs\Tag\Post\CachePopularTagsJob::class.'@handle',
            function ($job, $app) {
                return $job->handle($app->make(\N1ebieski\ICore\Models\Tag\Post\Tag::class));
            }
        );
    }
}
