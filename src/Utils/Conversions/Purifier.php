<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class Purifier implements Handler
{
    /**
     *
     * @param mixed $value
     * @param Closure $next
     * @return mixed
     */
    public function handle($value, Closure $next): mixed
    {
        return $next(\Mews\Purifier\Facades\Purifier::clean($value));
    }
}
