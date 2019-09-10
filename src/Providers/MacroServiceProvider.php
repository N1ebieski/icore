<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [MacroServiceProvider description]
 */
class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        Collection::macro('isEmptyItems', function() {
            return $this->every(function($value, $key) {
                return strlen($value) === 0;
            });
        });

        Collection::macro('isNullItems', function() {
            return $this->every(function($value, $key) {
                return $value === null;
            });
        });

        Str::macro('escaped', function($value) {
            $value = str_replace('*', '', $value);
            $value = preg_quote($value, '/');
            $value = str_replace('\|', '|', $value);

            return $value;
        });
    }
}
