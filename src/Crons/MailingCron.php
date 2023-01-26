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

namespace N1ebieski\ICore\Crons;

use Throwable;
use Carbon\Carbon;
use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\Jobs\SendMailingJob;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;
use Illuminate\Contracts\Container\BindingResolutionException;

class MailingCron
{
    /**
     *
     * @var Mailing
     */
    protected $mailing;

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
        protected MailingEmail $mailingEmail,
        protected Config $config,
        protected Carbon $carbon,
        protected SendMailingJob $sendMailingJob
    ) {
        $this->setDelay($this->carbon->now());

        $this->mailing = $this->mailingEmail->mailing()->make();
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
     *
     * @param MailingEmail $mailingEmail
     * @return void
     */
    protected function addToQueue(MailingEmail $mailingEmail): void
    {
        $this->sendMailingJob->dispatch($mailingEmail)->delay($this->delay);
    }

    /**
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Throwable
     */
    protected function activateScheduled(): void
    {
        $this->mailing->makeService()->activateScheduled();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function progressActivated(): void
    {
        $this->mailing->makeService()->progressActivated();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function deactivateCompleted(): void
    {
        $this->mailing->makeService()->deactivateCompleted();
    }
}
