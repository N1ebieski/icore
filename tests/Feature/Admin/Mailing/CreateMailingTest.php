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

namespace N1ebieski\ICore\Tests\Feature\Admin\Mailing;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateMailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.mailing.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertOk()
            ->assertViewIs('icore::admin.mailing.create')
            ->assertSee(route('admin.mailing.store'), false);
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.mailing.store'));

        $response->assertRedirect(route('login'));
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'), [
            'title' => '',
            'emails' => 'true',
            'status' => Status::INACTIVE,
            'emails_json' => '',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title', 'emails_json']);
    }

    public function testStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'), [
            'title' => 'Mailing dodany.',
            'content_html' => 'Ten mailing został dodany.',
            'emails' => 'true',
            'status' => Status::INACTIVE,
            'emails_json' => '[{"email": "dasds@dsdada.pl"}]'
        ]);

        $response->assertSessionHas('success');

        /** @var MailingLang|null */
        $mailingLang = MailingLang::where('title', 'Mailing dodany.')->first();

        $this->assertTrue($mailingLang?->exists());

        $response->assertRedirect(route('admin.mailing.index', [
            'filter' => [
                'search' => "id:\"{$mailingLang->mailing->id}\""
            ]
        ]));

        $this->assertDatabaseHas('mailings_emails', [
            'email' => 'dasds@dsdada.pl',
            'model_id' => null,
            'model_type' => null
        ]);
    }
}
