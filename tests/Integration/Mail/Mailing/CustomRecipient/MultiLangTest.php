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

namespace N1ebieski\ICore\Tests\Integration\Mail\Mailing\CustomRecipient;

use Tests\TestCase;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Mail\Mailing\Mail;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Container\BindingResolutionException;

class MultiLangTest extends TestCase
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

        App::setLocale('pl');
        Config::set('icore.multi_langs', ['pl', 'en']);
    }

    public function testViewData(): void
    {
        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->withoutLangs()->create();

        $langs = ['en', 'pl'];

        foreach ($langs as $lang) {
            /** @var array<string, MailingLang> $mailingsLang */
            $mailingsLang[$lang] = MailingLang::makeFactory()->for($mailing)->create([
                'lang' => $lang
            ]);
        }

        /** @var MailingEmail */
        $mailingEmail = MailingEmail::makeFactory()->email()->for($mailing)->create();

        /** @var Mail */
        $mail = $this->app->make(Mail::class, [
            'mailingEmail' => $mailingEmail
        ])->build();

        $this->assertStringStartsWith($mailingsLang['pl']->replacement_content_html, $mail->viewData['content']);
        $this->assertStringContainsString($mailingsLang['en']->replacement_content_html, $mail->viewData['content']);

        $this->assertNull($mail->viewData['subcopy']);
    }
}
