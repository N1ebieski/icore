<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Console\Commands\Update;

use Exception;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class SchemaFactory
{
    /**
     * Constructor.
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
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
