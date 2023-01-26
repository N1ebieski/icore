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

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.mailing.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateStatusWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateStatusNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateStatusValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => Status::INACTIVE,
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
            'status' => Status::INACTIVE,
        ]);
    }
}
