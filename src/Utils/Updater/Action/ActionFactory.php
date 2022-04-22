<?php

namespace N1ebieski\ICore\Utils\Updater\Action;

use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Utils\Updater\Action\Types\Interfaces\ActionInterface;

class ActionFactory
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
