<?php

namespace N1ebieski\ICore\Crons;

use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\Jobs\SendMailing;

/**
 * [MailingCron description]
 */
class MailingCron
{
    /**
     * [private description]
     * @var Mailing
     */
    protected $mailing;

    /**
     * [private description]
     * @var SendMailing
     */
    protected $sendMailing;

    /**
     * [__construct description]
     * @param Mailing     $mailing     [description]
     * @param SendMailing $sendMailing [description]
     */
    public function __construct(Mailing $mailing, SendMailing $sendMailing)
    {
        $this->mailing = $mailing;
        $this->sendMailing = $sendMailing;
    }

    /**
     * [__invoke description]
     */
    public function __invoke() : void
    {
        $this->activateScheduled();

        $this->addToQueue();
    }

    /**
     * Adds new jobs to the queue or (if there are none), disables mailing.
     */
    private function addToQueue() : void
    {
        $mailings = $this->mailing->makeRepo()->getActiveWithUnsentEmails();

        foreach ($mailings as $mailing) {
            if ($mailing->emails->isNotEmpty()) {
                foreach ($mailing->emails as $email) {
                    $this->sendMailing->dispatch($email);
                }
            }
            else {
                $mailing->update([
                    'status' => 0,
                    'activation_at' => null
                ]);
            }
        }
    }

    /**
     * Activates all scheduled mailings with a date earlier than now()
     *
     * @return int [description]
     */
    private function activateScheduled() : int
    {
        return $this->mailing->makeRepo()->activateScheduled();
    }
}
