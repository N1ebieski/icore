<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class Replacement implements Handler
{
    /**
     * Undocumented variable
     *
     * @var Collect
     */
    private $collect;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $replacement;

    /**
     * Undocumented function
     *
     * @param Collect $collect
     * @param array $replacement
     */
    public function __construct(Collect $collect, array $replacement)
    {
        $this->collect = $collect;

        $this->replacement = $replacement;
    }

    /**
     * Undocumented function
     *
     * @param [type] $value
     * @param Closure $next
     * @return void
     */
    public function handle($value, Closure $next)
    {
        $replacement = $this->collect->make($this->replacement);

        return str_replace(
            $replacement->keys()->toArray(),
            $replacement->values()->toArray(),
            $next($value)
        );
    }
}
