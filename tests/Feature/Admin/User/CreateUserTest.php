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

namespace N1ebieski\ICore\Tests\Feature\Admin\User;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.user.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.create'));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.user.store'),
            $baseResponse->getData()->view
        );
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.user.store'));

        $response->assertRedirect(route('login'));
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.user.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.user.store'), [
            'name' => '',
            'email' => 'dasdfd'
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'roles']);
    }

    public function testStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $this->get(route('admin.user.index'));

        $response = $this->post(route('admin.user.store'), [
            'name' => 'Bungo',
            'email' => 'dasd@fdfdfdf.pl',
            'roles' => [Name::USER, Name::ADMIN],
            'password' => 'bungoslaw',
            'password_confirmation' => 'bungoslaw'
        ]);

        $response->assertOk()->assertSessionHas('success');

        /** @var User */
        $us = User::where([
            ['name', 'Bungo'],
            ['email', 'dasd@fdfdfdf.pl']
        ])->first();

        $this->assertTrue($us->exists());

        $this->assertDatabaseHas('roles_models', [
            'model_id' => $us->id,
            'model_type' => $us->getMorphClass(),
        ]);
    }
}
