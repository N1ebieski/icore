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
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\User\Status;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserIndexAsGuest(): void
    {
        $response = $this->get(route('admin.user.index'));

        $response->assertRedirect(route('login'));
    }

    public function testUserIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<User>|array<User> */
        $users = User::makeFactory()->count(50)
            ->sequence(function (Sequence $sequence) {
                return [
                    'created_at' => Carbon::now()->addSeconds($sequence->index)
                ];
            })
            ->active()
            ->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.user.index')
            ->assertSee('class="pagination"', false)
            ->assertSeeInOrder([$users[30]->name, $users[30]->email], false);
    }

    public function testUserUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.user.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserUpdateStatusWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserUpdateStatusSelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$user->id]), [
            'status' => Status::INACTIVE,
        ]);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserUpdateStatusValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$us->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testUserUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$us->id]), [
            'status' => Status::INACTIVE,
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
            'status' => Status::INACTIVE,
        ]);
    }

    public function testUserDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.user.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserDestroySelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserDestroy(): void
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

    public function testUserDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testUserDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserDestroyGlobalValidationFail(): void
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

    public function testUserDestroyGlobalWithSelf(): void
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

    public function testUserDestroyGlobal(): void
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

    public function testUserEditAsGuest(): void
    {
        $response = $this->get(route('admin.user.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserEditSelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserEdit(): void
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

    public function testUserUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.user.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserUpdateSelf(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserUpdateValidationFail(): void
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

    public function testUserUpdate(): void
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

    public function testUserCreateAsGuest(): void
    {
        $response = $this->get(route('admin.user.create'));

        $response->assertRedirect(route('login'));
    }

    public function testUserCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserCreate(): void
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

    public function testUserStoreAsGuest(): void
    {
        $response = $this->post(route('admin.user.store'));

        $response->assertRedirect(route('login'));
    }

    public function testUserStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.user.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserStoreValidationFail(): void
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

    public function testUserStore(): void
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
