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

namespace N1ebieski\ICore\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collect;

abstract class Filter
{
    /**
     * [public description]
     * @var array
     */
    public array $parameters;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Collect $collect
     */
    public function __construct(Request $request, protected Collect $collect)
    {
        $this->setFilters((array)$request->input('filter'));
    }

    /**
     * [setFilters description]
     * @param  array $attributes [description]
     * @return self              [description]
     */
    public function setFilters(array $attributes): self
    {
        $filters = class_uses(static::class);

        if ($filters === false) {
            return $this;
        }

        foreach ($filters as $filter) {
            $filterName = $this->filterName($filter);
            $methodName = $this->methodName($filterName);

            if (method_exists($this, $methodName)) {
                $this->$methodName(
                    array_key_exists($filterName, $attributes) ?
                    (isset($attributes[$filterName]) ? $attributes[$filterName] : null)
                    : null
                );
            }
        }

        return $this;
    }

    /**
     * [methodName description]
     * @param  string $value [description]
     * @return string        [description]
     */
    protected function methodName(string $value): string
    {
        return 'filter' . ucfirst($value);
    }

    /**
     * [filterName description]
     * @param  string $value [description]
     * @return string        [description]
     */
    protected function filterName(string $value): string
    {
        return strtolower(str_replace('Has', '', class_basename($value)));
    }

    /**
     * [all description]
     * @return array [description]
     */
    public function all(): array
    {
        return (array)$this->parameters;
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public function get(string $parameter): mixed
    {
        return $this->parameters[$parameter] ?? null;
    }

    /**
     * Check if all parameters are null
     *
     * @return bool [description]
     */
    public function isNull(): bool
    {
        if ($this->parameters) {
            if (
                !array_filter($this->parameters, function ($value) {
                    return $value === null;
                })
            ) {
                return false;
            }
        }

        return true;
    }
}
