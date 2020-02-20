<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\MailingEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Closure;

class MailingEmailRepo
{
    /**
     * [private description]
     * @var MailingEmail
     */
    protected $mailingEmail;

    /**
     * [__construct description]
     * @param MailingEmail $mailingEmail [description]
     */
    public function __construct(MailingEmail $mailingEmail)
    {
        $this->mailingEmail = $mailingEmail;
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @return bool
     */
    public function chunkUnsentHasActiveMailing(Closure $closure) : bool
    {
        return $this->mailingEmail->where('send', 0)
            ->whereHas('mailing', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('mailing_id', 'asc')
            ->chunk(1000, $closure);
    }
}
