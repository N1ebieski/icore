<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use N1ebieski\ICore\Models\BanValue;

/**
 * Checks if the user's ip is banned
 */
class BanIp
{
    /**
     * [private description]
     * @var BanValue
     */
    protected $banValue;

    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     */
    public function __construct(BanValue $banValue)
    {
        $this->banValue = $banValue;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $bans = $this->banValue->makeCache()->rememberAllIpsAsString();

        if (!empty($bans) && preg_match('/^('.$bans.')/i', $request->ip())) {
            return abort(403, 'You cannot perform this action because you are banned.');
        }

        return $next($request);
    }
}
