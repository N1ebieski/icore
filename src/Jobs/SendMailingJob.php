<?php

namespace N1ebieski\ICore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\ICore\Models\MailingEmail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Mail\Mailing\Mail as MailingMail;
use Exception;
use N1ebieski\ICore\Models\Mailing;

/**
 * [SendMailing description]
 */
class SendMailingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * [protected description]
     * @var MailingEmail
     */
    protected $mailingEmail;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Undocumented function
     *
     * @param MailingEmail $mailingEmail
     */
    public function __construct(MailingEmail $mailingEmail)
    {
        $this->mailingEmail = $mailingEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(App $app, Mailer $mailer)
    {
        $this->app = $app;
        $this->mailer = $mailer;

        if ($this->mailingEmail->sent === MailingEmail::UNSENT) {
            if ($this->mailingEmail->mailing->status === Mailing::INPROGRESS) {
                $this->mailer->send(
                    $this->app->make(MailingMail::class, ['mailingEmail' => $this->mailingEmail])
                );

                $this->mailingEmail->makeRepo()->markAsSent();
            }
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->mailingEmail->makeRepo()->markAsError();
    }
}
