<?php

namespace N1ebieski\ICore\Console\Commands\Update;

use Exception;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class SchemaFactory
{
    /**
     * @var App
     */
    protected $app;

    /**
     * Constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isClassExists(string $type): bool
    {
        return class_exists($this->className($type)) || $this->app->bound($this->className($type));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className(string $type): string
    {
        return "N1ebieski\\ICore\\Utils\\Updater\\Schema\\Schema" . ucfirst($type);
    }

    /**
     *
     * @param string $version
     * @return SchemaInterface
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function makeSchema(string $version): SchemaInterface
    {
        $version = str_replace('.', '', $version);

        if ($this->isClassExists($version)) {
            return $this->app->make($this->className($version));
        }

        throw new \Exception("Schema \"{$version}\" not found");
    }
}
