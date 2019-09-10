<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Localization Carbon
        // Carbon::setUTF8(true);
        // Carbon::setLocale(config('app.locale'));
        date_default_timezone_set('Europe/Warsaw');
        setlocale(LC_ALL, Config::get('icore.app.locale_full'));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Hook sprawiający, że Laravel tworząc linki przez route() używa na sztywno
        // adresu z configu zamiast z HTTP_HOST. Potrzebne dla browsersync-a, bo
        // działa na proxy na porcie 3000 i dla żądań przez ajax rzuca błędami o CORS.
        // Wyłączyć przy wrzucaniu na produkcję!
        URL::forceRootUrl(Config::get('app.url'));
    }
}
