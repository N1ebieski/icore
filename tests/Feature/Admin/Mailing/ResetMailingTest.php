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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Models\MailingEmail\User\MailingEmail;

class ResetMailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testResetAsGuest(): void
    {
        $response = $this->delete(route('admin.mailing.reset', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testResetWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testResetNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.reset', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testReset(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $user2 = User::makeFactory()->create();

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

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]), []);

        $response->assertOk();

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseMissing('mailings_emails', [
            'id' => $email->id,
        ]);
    }
}
