<?php

namespace N1ebieski\ICore\Crons;

use N1ebieski\ICore\Models\MailingEmail;
use N1ebieski\ICore\Jobs\SendMailingJob;

/**
 * [MailingCron description]
 */
class MailingCron
{
    /**
     * [private description]
     * @var MailingEmail
     */
    protected $mailingEmail;

    /**
     * [private description]
     * @var SendMailingJob
     */
    protected $sendMailingJob;

    /**
     * [__construct description]
     * @param MailingEmail     $mailingEmail     [description]
     * @param SendMailingJob   $sendMailingJob   [description]
     */
    public function __construct(MailingEmail $mailingEmail, SendMailingJob $sendMailingJob)
    {
        $this->mailingEmail = $mailingEmail;

        $this->sendMailingJob = $sendMailingJob;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->activateScheduled();

        $this->mailingEmail->makeRepo()->chunkUnsentHasActiveMailing(
            function ($items) {
                $items->each(function ($item) {
                    $this->sendMailingJob->dispatch($item);
                });
            }
        );

        $this->deactivateCompleted();
    }

    /**
     * Adds new jobs to the queue or (if there are none), disables mailing.
     *
     * @param MailingEmail   $mailingEmail   [description]
     */
    protected function addToQueue(MailingEmail $mailingEmail) : void
    {
        $this->sendMailingJob->dispatch($mailingEmail);
    }

    /**
     * Activates all scheduled mailings with a date earlier than now()
     *
     * @return void
     */
    protected function activateScheduled() : void
    {
        $this->mailingEmail->mailing()->make()->makeRepo()->activateScheduled();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function deactivateCompleted() : void
    {
        $this->mailingEmail->mailing()->make()->makeRepo()->deactivateCompleted();
    }
}
