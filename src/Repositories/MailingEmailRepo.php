<?php

namespace N1ebieski\ICore\Repositories;

use N1ebieski\ICore\Models\MailingEmail;
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
     * @return boolean
     */
    public function markAsSent() : bool
    {
        return $this->mailingEmail->update(['sent' => MailingEmail::SENT]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function markAsError() : bool
    {
        return $this->mailingEmail->update(['sent' => MailingEmail::ERROR]);
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return bool
     */
    public function chunkUnsentHasActiveMailing(Closure $callback) : bool
    {
        return $this->mailingEmail->unsent()
            ->whereHas('mailing', function ($query) {
                $query->active();
            })
            ->orderBy('mailing_id', 'asc')
            ->chunk(1000, $callback);
    }
}
