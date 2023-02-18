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

namespace N1ebieski\ICore\Tests\Feature\Admin\BanValue;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateBanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('admin.banvalue.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testEditNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.banvalue.update', [$banvalue->id]),
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            $banvalue->value,
            $baseResponse->getData()->view
        );
    }

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.banvalue.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testUpdateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => ''
        ]);

        $response->assertSessionHasErrors(['value']);
    }

    public function testUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        $new_ip = '32.343.54.232';

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => $new_ip
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString($new_ip, $baseResponse->getData()->view);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
            'value' => $new_ip
        ]);
    }
}
