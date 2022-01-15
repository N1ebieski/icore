<?php

namespace N1ebieski\ICore\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;

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
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
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

        Collection::macro('flattenRelation', function (string $relation) {
            $result = $this->make([]);

            $closure = function ($collection) use (&$closure, &$result, $relation) {
                $collection->each(function ($item) use (&$closure, &$result, $relation) {
                    $value = clone $item;
                    unset($value->{$relation});

                    $result->push($value);

                    if ($item->relationLoaded($relation)) {
                        $closure($item->{$relation});
                    }
                });
            };

            $closure($this);

            return $result->filter();
        });

        Collection::macro('isEmptyItems', function () {
            return $this->every(function ($value, $key) {
                return strlen($value) === 0;
            });
        });

        Collection::macro('isNullItems', function () {
            return $this->every(function ($value, $key) {
                return $value === null;
            });
        });

        Str::macro('randomColor', function (string $value) {
            $hash = md5('color' . $value);

            $rgb = [
                hexdec(substr($hash, 0, 2)),
                hexdec(substr($hash, 2, 2)),
                hexdec(substr($hash, 4, 2))
            ];

            return 'rgb(' . implode(', ', $rgb) . ')';
        });

        Str::macro('escaped', function ($value) {
            $value = str_replace('*', '', $value);
            $value = preg_quote($value, '/');
            $value = str_replace('\|', '|', $value);

            return $value;
        });
    }
}
