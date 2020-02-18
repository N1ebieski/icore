<?php

namespace N1ebieski\ICore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\ICore\Models\MailingEmail;
use Illuminate\Support\Facades\Mail;
use N1ebieski\ICore\Mail\Mailing\Mail as MailingMail;
use Exception;

/**
 * [SendMailing description]
 */
class SendMailing implements ShouldQueue
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
    protected $email;

    /**
     * Create a new job instance.
     *
     * @param MailingEmail $email
     * @return void
     */
    public function __construct(MailingEmail $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->email->send === 0) {
            if ($this->email->mailing->status === 1) {
                Mail::send(app()->makeWith(MailingMail::class, ['email' => $this->email]));
                $this->email->update(['send' => 1]);
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
        $this->email->update(['send' => 2]);
    }
}
