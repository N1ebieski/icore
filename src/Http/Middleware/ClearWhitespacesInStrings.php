<?php

namespace N1ebieski\ICore\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * [ConvertEmptyStringsToNull description]
 */
class ClearWhitespacesInStrings extends TransformsRequest
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $except = ['content_html'];

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (!in_array($key, $this->except) && is_string($value)) {
            $value = str_replace("&nbsp;", " ", $value);
            $value = preg_replace('/[[:blank:]]+/', ' ', $value);
        }

        return $value;
    }
}
