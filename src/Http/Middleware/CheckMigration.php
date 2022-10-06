<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Http\Response as HttpResponse;

class CheckMigration
{
    /**
     * [__construct description]
     * @param MigrationUtil $migrationUtil [description]
     */
    public function __construct(protected MigrationUtil $migrationUtil)
    {
        //
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
