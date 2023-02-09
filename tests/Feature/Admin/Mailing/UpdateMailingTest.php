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
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateMailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('admin.mailing.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertOk()
            ->assertViewIs('icore::admin.mailing.edit')
            ->assertSeeInOrder([$mailing->title, $mailing->content], false)
            ->assertSee(route('admin.mailing.update', [$mailing->id]), false);
    }

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.mailing.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]), [
            'title' => '',
            'status' => 2,
            'content_html' => 'Ten mailing został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title', 'date_activation_at', 'time_activation_at']);
    }

    public function testUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $user2 = User::makeFactory()->marketing()->active()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]), [
            'title' => 'Mailing zaktualizowany.',
            'content_html' => 'Ten mailing został zaktualizowany.',
            'status' => Status::ACTIVE,
            'users' => 'true'
        ]);

        $response->assertRedirect(route('admin.mailing.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mailings_langs', [
            'content' => 'Ten mailing został zaktualizowany.',
            'title' => 'Mailing zaktualizowany.',
            'mailing_id' => $mailing->id
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'model_id' => $user2->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\User',
            'email' => $user2->email
        ]);
    }
}
