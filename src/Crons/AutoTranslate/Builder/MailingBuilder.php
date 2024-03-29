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

namespace N1ebieski\ICore\Crons\AutoTranslate\Builder;

use Closure;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Crons\AutoTranslate\Builder\Interfaces\BuilderInterface;

class MailingBuilder implements BuilderInterface
{
    /**
     *
     * @param Mailing $mailing
     * @return void
     */
    public function __construct(protected Mailing $mailing)
    {
        //
    }

    /**
     *
     * @param Closure $closure
     * @param string $timestamp
     * @return bool
     * @throws BindingResolutionException
     */
    public function chunkCollection(Closure $closure, string $timestamp): bool
    {
        return $this->mailing->makeRepo()->chunkAutoTransWithLangsByTranslatedAt($closure, $timestamp);
    }
}
