<?php

namespace N1ebieski\ICore\Repositories;

use Closure;
use N1ebieski\ICore\Models\MailingEmail;

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
    public function markAsSent(): bool
    {
        return $this->mailingEmail->update(['sent' => MailingEmail::SENT]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function markAsError(): bool
    {
        return $this->mailingEmail->update(['sent' => MailingEmail::ERROR]);
    }

    /**
     * Undocumented function
     *
     * @param integer $chunk
     * @param Closure $callback
     * @return boolean
     */
    public function chunkUnsentHasProgressMailing(int $chunk, Closure $callback): bool
    {
        return $this->mailingEmail->unsent()
            ->whereHas('mailing', function ($query) {
                $query->progress();
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
        return $this->mailingEmail->latest('id')->first();
    }
}
