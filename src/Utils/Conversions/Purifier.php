<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class Purifier implements Handler
{
    /**
     * Undocumented function
     *
     * @param [type] $value
     * @param Closure $next
     * @return void
     */
    public function handle($value, Closure $next)
    {
        return $next(\Mews\Purifier\Facades\Purifier::clean($value));
    }
}
