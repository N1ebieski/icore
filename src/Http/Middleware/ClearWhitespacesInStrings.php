<?php

namespace N1ebieski\ICore\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * [ConvertEmptyStringsToNull description]
 */
class ClearWhitespacesInStrings extends TransformsRequest
{
    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (is_string($value)) {
            $value = str_replace("&nbsp;", " ", $value);
            $value = preg_replace('/[[:blank:]]+/', ' ', $value);
        }

        return $value;
    }
}
