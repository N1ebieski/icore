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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Models\MailingEmail\User\MailingEmail;

class DeleteMailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.mailing.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        /** @var MailingEmail */
        $email = MailingEmail::makeFactory()->withMorph()->for($mailing)->create();

        Auth::login($user);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
        ]);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseMissing('mailings_emails', [
            'id' => $email->id,
        ]);
    }

    public function testDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Mailing> */
        $mailing = Mailing::makeFactory()->count(10)->create();

        Auth::login($user);

        $this->get(route('admin.mailing.index'));

        $select = collect($mailing)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.mailing.destroy_global'), [
            'select' => $select
        ]);

        $response->assertRedirect(route('admin.mailing.index'));
        $response->assertSessionHas('success');

        $deleted = Mailing::whereIn('id', $select)->get();

        $this->assertTrue($deleted->count() === 0);
    }
}
