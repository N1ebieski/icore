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
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateBanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.banvalue.create', [Type::IP]));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', [Type::IP]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoExistTypeCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', ['dasdad']));

        $response->assertSessionHasErrors(['type']);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', [Type::IP]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.banvalue.store', [Type::IP]),
            $baseResponse->getData()->view
        );
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.banvalue.store', [Type::IP]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoExistTypeStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', ['dasdada']), []);

        $response->assertSessionHasErrors(['type']);
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', [Type::IP]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banmodel = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', [Type::IP]), [
            'value' => $banmodel->value,
        ]);

        $response->assertSessionHasErrors(['value']);
    }

    public function testStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', [Type::IP]), [
            'value' => '22.222.22.22',
        ]);

        $response->assertOk();
        $response->assertSessionHas(['success' => trans('icore::bans.value.success.store')]);

        $this->assertDatabaseHas('bans_values', [
            'value' => '22.222.22.22',
        ]);
    }
}
