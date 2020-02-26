<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Mews\Purifier\Facades\Purifier;

class XSSProtection
{

    /**
     * Tablica zawierajca klucze requestow ktore maja byc pomijane przy strip_tags,
     * zamiast tego wykonywany jest na nich clean przez HTML Purifier
     * @var array
     */
    protected $except = ['content_html'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (!in_array(strtolower($request->method()), ['put', 'post'])) {
        //     return $next($request);
        // }

        $input = $request->all();

        array_walk_recursive($input, function (&$value, &$key) {
            if (in_array($key, $this->except, true)) {
                $value = Purifier::clean($value);
            } else {
                $value = strip_tags($value);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
