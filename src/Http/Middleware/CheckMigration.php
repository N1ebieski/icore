<?php

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Http\Response as HttpResponse;

class CheckMigration
{
    /**
     * [private description]
     * @var MigrationUtil
     */
    protected $migrationUtil;

    /**
     * [__construct description]
     * @param MigrationUtil $migrationUtil [description]
     */
    public function __construct(MigrationUtil $migrationUtil)
    {
        $this->migrationUtil = $migrationUtil;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $migration
     * @return mixed
     */
    public function handle($request, Closure $next, string $migration)
    {
        if (!$this->migrationUtil->contains($migration)) {
            return App::abort(HttpResponse::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
