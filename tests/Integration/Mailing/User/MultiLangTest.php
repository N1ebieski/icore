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

namespace N1ebieski\ICore\Tests\Integration\Mailing\User;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Mail\Mailing\Mail;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Models\MailingEmail\User\MailingEmail;
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
        URL::defaults(['lang' => 'pl']);
        Config::set('icore.multi_langs', ['pl', 'en']);
    }

    public function testViewDataIfPrefLangExists(): void
    {
        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->withoutLangs()->create();

        foreach (['pl', 'en'] as $lang) {
            /** @var array<MailingLang> */
            $mailingsLang[$lang] = MailingLang::makeFactory()->for($mailing)->create([
                'lang' => $lang
            ]);
        }

        /** @var User */
        $user = User::makeFactory()->active()->user()->create([
            'pref_lang' => 'en'
        ]);

        /** @var MailingEmail */
        $mailingEmail = MailingEmail::makeFactory()->for($mailing)->for($user, 'morph')->create();

        /** @var Mail */
        $mail = $this->app->make(Mail::class, [
            'mailingEmail' => $mailingEmail
        ])->build();

        App::setLocale('en');

        $this->assertTrue($mail->viewData['content'] === $mailingsLang['en']->replacement_content_html);
        $this->assertTrue($mail->viewData['subcopy'] === Lang::get('icore::newsletter.subcopy.user', [
            'cancel' => URL::route('web.profile.edit')
        ]));
    }

    public function testViewDataIfPrefLangDoesntExist(): void
    {
        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->withoutLangs()->create();

        $langs = ['en', 'pl'];

        foreach ($langs as $lang) {
            /** @var array<MailingLang> */
            $mailingsLang[$lang] = MailingLang::makeFactory()->for($mailing)->create([
                'lang' => $lang
            ]);
        }

        /** @var User */
        $user = User::makeFactory()->active()->user()->create([
            'pref_lang' => 'de'
        ]);

        /** @var MailingEmail */
        $mailingEmail = MailingEmail::makeFactory()->for($mailing)->for($user, 'morph')->create();

        /** @var Mail */
        $mail = $this->app->make(Mail::class, [
            'mailingEmail' => $mailingEmail
        ])->build();

        $this->assertStringStartsWith($mailingsLang['pl']->replacement_content_html, $mail->viewData['content']);
        $this->assertStringContainsString($mailingsLang['en']->replacement_content_html, $mail->viewData['content']);
    }
}
