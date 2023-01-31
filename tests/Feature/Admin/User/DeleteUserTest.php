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
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.user.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroySelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
        ]);

        $response = $this->delete(route('admin.user.destroy', [$us->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('users', [
            'id' => $us->id,
        ]);
    }

    public function testDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testDestroyGlobalWithSelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Collection<User> */
        $users = User::makeFactory()->count(10)->active()->user()->create();

        Auth::login($user);

        $this->get(route('admin.user.index'));

        $response = $this->delete(route('admin.user.destroy_global'), [
            'select' => $users->push($user)->pluck('id')->toArray(),
        ]);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Collection<User> */
        $users = User::makeFactory()->count(10)->active()->user()->create();

        Auth::login($user);

        $this->get(route('admin.user.index'));

        $select = collect($users)->pluck('id')->toArray();

        $response = $this->delete(route('admin.user.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $deleted = Post::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);
    }
}
