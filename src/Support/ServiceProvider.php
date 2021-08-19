<?php

namespace N1ebieski\ICore\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if the configs should be merged recursivelyly.
     *
     * @var bool
     */
    protected $recursivelyMergeConfigs = true;

    /**
     * Undocumented function
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Override. Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (!$this->app->configurationIsCached()) {
            $this->app['config']->set($key, $this->mergeConfigs(
                require $path,
                $this->app['config']->get($key, [])
            ));
        }
    }

    /**
     * Merges the 2 given configs together, and if supplied, it will do it recursively.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfigs($original, $merging)
    {
        $array = array_merge($original, $merging);

        if (!$this->recursivelyMergeConfigs) {
            return $array;
        }

        foreach ($original as $key => $value) {
            if (!is_array($value)) {
                continue;
            }

            if (!Arr::exists($merging, $key)) {
                continue;
            }

            if ($this->isContainsStringKey($value) === false) {
                continue;
            }

            if (is_integer($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfigs($value, $merging[$key]);
        }

        return $array;
    }

    /**
     * Undocumented function
     *
     * @param array $array
     * @return boolean
     */
    protected function isContainsStringKey(array $array): bool
    {
        return Collect::make($array)
            ->contains(function ($value, $key) {
                return is_string($key);
            });
    }
}
