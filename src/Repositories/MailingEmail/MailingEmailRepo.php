<?php

namespace N1ebieski\ICore\Repositories\MailingEmail;

use Closure;
use N1ebieski\ICore\ValueObjects\MailingEmail\Sent;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;

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
        return $this->mailingEmail->update(['sent' => Sent::SENT]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function markAsError(): bool
    {
        return $this->mailingEmail->update(['sent' => Sent::ERROR]);
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
        return $this->mailingEmail->unsent()
            ->whereHas('mailing', function ($query) {
                $query->active();
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
