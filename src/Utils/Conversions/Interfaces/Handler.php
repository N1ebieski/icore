<?php

namespace N1ebieski\ICore\Utils\Conversions\Interfaces;

use Closure;

interface Handler
{
    /**
     * Undocumented function
     *
     * @param [type] $value
     * @param Closure $next
     * @return void
     */
    public function handle($value, Closure $next);
}
