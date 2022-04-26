<?php

namespace N1ebieski\ICore\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\ICore\Mail\Mailing\Mail as MailingMail;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;

class SendMailingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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

        if ($this->mailingEmail->sent->isUnsent()) {
            if ($this->mailingEmail->mailing->status->isInprogress()) {
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
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->mailingEmail->makeRepo()->markAsError();
    }
}
