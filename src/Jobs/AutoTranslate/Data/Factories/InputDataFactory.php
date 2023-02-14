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

namespace N1ebieski\ICore\Jobs\AutoTranslate\Data\Factories;

use Exception;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Models\Interfaces\TransableInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Interfaces\InputDataInterface;

class InputDataFactory
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
     *
     * @param TransableInterface $model
     * @return bool
     */
    protected function isClassExists(TransableInterface $model): bool
    {
        return class_exists($this->className($model)) || $this->app->bound($this->className($model));
    }

    /**
     *
     * @param TransableInterface $model
     * @return string
     */
    protected function className(TransableInterface $model): string
    {
        $className = $this->getBaseName($model);

        return "N1ebieski\\ICore\\Jobs\\AutoTranslate\\Data\\" . $className . "\\" . $className . "InputData";
    }

    /**
     *
     * @param TransableInterface $model
     * @return string
     */
    protected function getBaseName(TransableInterface $model): string
    {
        return class_basename($model::class);
    }

    /**
     *
     * @param TransableInterface $model
     * @return InputDataInterface
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function makeData(TransableInterface $model): InputDataInterface
    {
        $modelName = lcfirst($this->getBaseName($model));

        if ($this->isClassExists($model)) {
            return $this->app->make($this->className($model), [$modelName => $model]);
        }

        throw new \Exception("Input data for \"{$this->getBaseName($model)}\" not found");
    }
}
