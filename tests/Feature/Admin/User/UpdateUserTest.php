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
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('admin.user.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditSelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$us->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            $us->name,
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            route('admin.user.update', [$us->id]),
            $baseResponse->getData()->view
        );
    }

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.user.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateSelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]), [
            'name' => '',
            'email' => 'dasdad@'
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function testUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]), [
            'name' => 'Bungo',
            'email' => 'email@bungoslawa.pl',
            'roles' => ['user']
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            'email@bungoslawa.pl',
            $baseResponse->getData()->view
        );

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
            'name' => 'Bungo',
            'email' => 'email@bungoslawa.pl',
        ]);
    }
}
