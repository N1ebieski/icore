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

namespace N1ebieski\ICore\Utils\Updater\Action;

use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Utils\Updater\Action\Types\Interfaces\ActionInterface;

class ActionFactory
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
        return "N1ebieski\\ICore\\Utils\\Updater\\Action\\Types\\" . ucfirst($type);
    }

    /**
     * Undocumented function
     *
     * @param array $action
     * @return ActionInterface
     */
    public function makeAction(array $action): ActionInterface
    {
        if ($this->isClassExists($action['type'])) {
            return $this->app->make($this->className($action['type']), ['action' => $action]);
        }

        throw new \Exception("Updater action \"{$action['type']}\" not found");
    }
}
