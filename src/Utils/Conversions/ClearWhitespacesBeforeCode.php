<?php

namespace N1ebieski\ICore\Utils\Conversions;

use Closure;
use Illuminate\Support\Str;
use N1ebieski\ICore\Utils\Conversions\Interfaces\Handler;

class ClearWhitespacesBeforeCode implements Handler
{
    /**
     *
     * @param Str $str
     * @return void
     */
    public function __construct(protected Str $str)
    {
        //
    }

    /**
     *
     * @param mixed $value
     * @param Closure $next
     * @return mixed
     */
    public function handle($value, Closure $next): mixed
    {
        return $next($this->str->of($value)->replaceMatches('/(\s+)<code/', '<code'));
    }
}
