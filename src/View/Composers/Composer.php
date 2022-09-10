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

namespace N1ebieski\ICore\View\Composers;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

abstract class Composer implements Arrayable
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $ignore = [];

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with($this->toArray());
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items()->all();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    protected function items(): Collection
    {
        $class = new ReflectionClass($this);

        $publicProperties = collect($class->getProperties(ReflectionProperty::IS_PUBLIC))
            ->reject(function (ReflectionProperty $property) {
                return $this->shouldIgnore($property->getName());
            })
            ->mapWithKeys(function (ReflectionProperty $property) {
                return [$property->getName() => $this->{$property->getName()}];
            });

        $publicMethods = collect($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(function (ReflectionMethod $method) {
                return $this->shouldIgnore($method->getName());
            })
            ->mapWithKeys(function (ReflectionMethod $method) {
                return [$method->getName() => $this->createVariableFromMethod($method)];
            });

        return $publicProperties->merge($publicMethods);
    }

    /**
     * Undocumented function
     *
     * @param string $methodName
     * @return boolean
     */
    protected function shouldIgnore(string $methodName): bool
    {
        if (Str::startsWith($methodName, '__')) {
            return true;
        }

        return in_array($methodName, $this->ignoredMethods());
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    protected function ignoredMethods(): array
    {
        return array_merge(['compose', 'toArray'], $this->ignore);
    }

    /**
     * Undocumented function
     *
     * @param ReflectionMethod $method
     * @return mixed
     */
    protected function createVariableFromMethod(ReflectionMethod $method)
    {
        if ($method->getNumberOfParameters() === 0) {
            return $this->{$method->getName()}();
        }

        // @phpstan-ignore-next-line
        return Closure::fromCallable([$this, $method->getName()]);
    }
}
