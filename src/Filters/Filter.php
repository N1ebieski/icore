<?php

namespace N1ebieski\ICore\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collect;

abstract class Filter
{
    /**
     * [protected description]
     * @var Collect
     */
    protected $collect;

    /**
     * [public description]
     * @var array
     */
    public $parameters;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Collect $collect
     */
    public function __construct(Request $request, Collect $collect)
    {
        $this->collect = $collect;

        $this->setFilters((array)$request->input('filter'));
    }

    /**
     * [setFilters description]
     * @param  array $attributes [description]
     * @return self              [description]
     */
    public function setFilters(array $attributes): self
    {
        foreach (class_uses(static::class) as $filter) {
            $filterName = $this->makeFilterName($filter);
            $methodName = $this->makeMethodName($filterName);

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
     * [makeMethodName description]
     * @param  string $value [description]
     * @return string        [description]
     */
    protected function makeMethodName(string $value): string
    {
        return 'filter' . ucfirst($value);
    }

    /**
     * [makeFilterName description]
     * @param  string $value [description]
     * @return string        [description]
     */
    protected function makeFilterName(string $value): string
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
     * [get description]
     * @param  string $parameter [description]
     * @return mixed             [description]
     */
    public function get(string $parameter)
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
