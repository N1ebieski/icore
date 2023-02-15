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

namespace N1ebieski\ICore\Tests\Integration\Crons\Mailing;

use Tests\TestCase;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Crons\MailingCron;
use Illuminate\Support\Facades\Artisan;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use N1ebieski\ICore\ValueObjects\MailingEmail\Sent;
use N1ebieski\ICore\Mail\Mailing\Mail as MailingMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Models\MailingEmail\User\MailingEmail;
use Illuminate\Contracts\Container\BindingResolutionException;

class SendMailingCronTest extends TestCase
{
    use DatabaseTransactions;

    /**
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('queue.default', 'database');
    }

    public function testCronIfEmailIsInvalid(): void
    {
        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->create();

        /** @var MailingEmail */
        $email = MailingEmail::makeFactory()->for($mailing)->create([
            'email' => ''
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => Sent::UNSENT
        ]);

        $schedule = app()->make(MailingCron::class);
        $schedule();

        Artisan::call('queue:work', ['--daemon' => true, '--tries' => 3, '--once' => true]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => Sent::ERROR
        ]);
    }

    public function testCron(): void
    {
        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->create();

        /** @var MailingEmail */
        $email = MailingEmail::makeFactory()->email()->for($mailing)->create();

        Mail::fake();

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => Sent::UNSENT
        ]);

        // Uruchamiamy zadanie crona bezpośrednio, bo przez schedule:run ma ustalony delay
        // (np. odpala się co godzinę)
        $schedule = app()->make(MailingCron::class);
        $schedule();

        Artisan::call('queue:work', ['--daemon' => true, '--tries' => 3, '--once' => true]);

        Mail::assertSent(MailingMail::class, function ($mail) use ($email, $mailing) {
            $mail->build();

            return $mail->hasTo($email->email) && $mail->subject($mailing->title);
        });

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => Sent::SENT
        ]);

        $schedule = app()->make(MailingCron::class);
        $schedule();

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
            'status' => Status::INACTIVE
        ]);
    }
}
