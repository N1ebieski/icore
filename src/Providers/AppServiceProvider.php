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
        $this->app->bind(\N1ebieski\ICore\Utils\Conversions\Replacement::class, function ($app) {
            return new \N1ebieski\ICore\Utils\Conversions\Replacement(
                new \Illuminate\Support\Collection,
                $app['config']->get('icore.replacement')
            );
        });

        $this->app->bind(\GuzzleHttp\Client::class, function ($app) {
            return new \GuzzleHttp\Client([
                'headers' => [
                    'User-Agent' => 'iCore v' . $this->app['config']->get('icore.version')
                    . ' ' . parse_url($this->app['config']->get('app.url'), PHP_URL_HOST)
                ],
                'timeout' => 10.0
            ]);
        });

        $this->app->bind(\Spatie\ArrayToXml\ArrayToXml::class, function ($app) {
            return new \Spatie\ArrayToXml\ArrayToXml([]);
        });

        $this->app->bindMethod(
            \N1ebieski\ICore\Jobs\Tag\Post\CachePopularTagsJob::class.'@handle',
            function ($job, $app) {
                return $job->handle($app->make(\N1ebieski\ICore\Models\Tag\Post\Tag::class));
            }
        );
    }
}
