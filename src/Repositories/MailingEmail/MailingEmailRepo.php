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

namespace N1ebieski\ICore\Repositories\MailingEmail;

use Closure;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;

class MailingEmailRepo
{
    /**
     * [__construct description]
     * @param MailingEmail $mailingEmail [description]
     */
    public function __construct(protected MailingEmail $mailingEmail)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param integer $chunk
     * @param Closure $callback
     * @return boolean
     */
    public function chunkUnsentHasActiveMailing(int $chunk, Closure $callback): bool
    {
        return $this->mailingEmail->newQuery()
            ->unsent()
            ->whereHas('mailing', function (Builder|Mailing $query) {
                return $query->active()->with('langs');
            })
            ->orderBy('mailing_id', 'asc')
            ->chunk($chunk, $callback);
    }

    /**
     * Undocumented function
     *
     * @return MailingEmail|null
     */
    public function firstByLatestId(): ?MailingEmail
    {
        return $this->mailingEmail->newQuery()->latest('id')->first();
    }
}
