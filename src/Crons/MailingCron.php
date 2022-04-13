<?php

namespace N1ebieski\ICore\Crons;

use Carbon\Carbon;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;
use N1ebieski\ICore\Jobs\SendMailingJob;
use Illuminate\Contracts\Config\Repository as Config;

class MailingCron
{
    /**
     * [private description]
     * @var MailingEmail
     */
    protected $mailingEmail;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * [private description]
     * @var SendMailingJob
     */
    protected $sendMailingJob;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $delay;

    /**
     * Undocumented function
     *
     * @param MailingEmail $mailingEmail
     * @param Config $config
     * @param Carbon $carbon
     * @param SendMailingJob $sendMailingJob
     */
    public function __construct(
        MailingEmail $mailingEmail,
        Config $config,
        Carbon $carbon,
        SendMailingJob $sendMailingJob
    ) {
        $this->mailingEmail = $mailingEmail;

        $this->carbon = $carbon;
        $this->config = $config;

        $this->setDelay($this->carbon->now());

        $this->sendMailingJob = $sendMailingJob;
    }

    /**
     * Undocumented function
     *
     * @param Carbon $delay
     * @return self
     */
    protected function setDelay(Carbon $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * [__invoke description]
     */
    public function __invoke(): void
    {
        $this->activateScheduled();

        $this->mailingEmail->makeRepo()->chunkUnsentHasActiveMailing(
            $this->config->get('icore.mailing.limit'),
            function ($items) {
                $items->each(function ($item) {
                    $this->addToQueue($item);
                });

                $this->setDelay(
                    $this->delay->addMinutes($this->config->get('icore.mailing.delay'))
                );
            }
        );

        $this->progressActivated();

        $this->deactivateCompleted();
    }

    /**
     * Adds new jobs to the queue or (if there are none), disables mailing.
     *
     * @param MailingEmail   $mailingEmail   [description]
     */
    protected function addToQueue(MailingEmail $mailingEmail): void
    {
        $this->sendMailingJob->dispatch($mailingEmail)->delay($this->delay);
    }

    /**
     * Activates all scheduled mailings with a date earlier than now()
     *
     * @return void
     */
    protected function activateScheduled(): void
    {
        $this->mailingEmail->mailing()->make()->makeRepo()->activateScheduled();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function progressActivated(): void
    {
        $this->mailingEmail->mailing()->make()->makeRepo()->progressActivated();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function deactivateCompleted(): void
    {
        $this->mailingEmail->mailing()->make()->makeRepo()->deactivateCompleted();
    }
}
