<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserIndexAsGuest()
    {
        $response = $this->get(route('admin.user.index'));

        $response->assertRedirect(route('login'));
    }

    public function testUserIndexWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserIndexPaginate()
    {
        $user = User::makeFactory()->admin()->create();

        $us = User::makeFactory()->count(50)->active()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|desc'
            ]
        ]));

        $response->assertViewIs('icore::admin.user.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$us[30]->name, $us[30]->email], false);
    }

    public function testUserUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.user.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserUpdateStatusWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserUpdateStatus()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserUpdateStatusSelf()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$user->id]), [
            'status' => 0,
        ]);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserUpdateStatusValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$us->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testUserUpdateStatus()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.user.update_status', [$us->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
            'status' => 0,
        ]);
    }

    public function testUserDestroyAsGuest()
    {
        $response = $this->delete(route('admin.user.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserDestroyWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserDestroySelf()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserDestroy()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserDestroy()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
        ]);

        $response = $this->delete(route('admin.user.destroy', [$us->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('users', [
            'id' => $us->id,
        ]);
    }

    public function testUserDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testUserDestroyGlobalWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserDestroyGlobalValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.user.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testUserDestroyGlobalWithSelf()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->count(10)->active()->user()->create();

        Auth::login($user);

        $this->get(route('admin.user.index'));

        $response = $this->delete(route('admin.user.destroy_global'), [
            'select' => $us->push($user)->pluck('id')->toArray(),
        ]);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserDestroyGlobal()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->count(10)->active()->user()->create();

        Auth::login($user);

        $this->get(route('admin.user.index'));

        $select = collect($us)->pluck('id')->toArray();

        $response = $this->delete(route('admin.user.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $deleted = Post::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);
    }

    public function testUserEditAsGuest()
    {
        $response = $this->get(route('admin.user.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserEditWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserEditSelf()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserEdit()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserEdit()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.edit', [$us->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($us->name, $response->getData()->view);
        $this->assertStringContainsString(route('admin.user.update', [$us->id]), $response->getData()->view);
    }

    public function testUserUpdateAsGuest()
    {
        $response = $this->put(route('admin.user.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserUpdateWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserUpdateSelf()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$user->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistUserUpdate()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserUpdateValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]), [
            'name' => '',
            'email' => 'dasdad@'
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function testUserUpdate()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $us = User::makeFactory()->active()->user()->create();

        Auth::login($user);

        $response = $this->put(route('admin.user.update', [$us->id]), [
            'name' => 'Bungo',
            'email' => 'email@bungoslawa.pl',
            'roles' => ['user']
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString('email@bungoslawa.pl', $response->getData()->view);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
            'name' => 'Bungo',
            'email' => 'email@bungoslawa.pl',
        ]);
    }

    public function testUserCreateAsGuest()
    {
        $response = $this->get(route('admin.user.create'));

        $response->assertRedirect(route('login'));
    }

    public function testUserCreateWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserCreate()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.user.create'));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.user.store'), $response->getData()->view);
    }

    public function testUserStoreAsGuest()
    {
        $response = $this->post(route('admin.user.store'));

        $response->assertRedirect(route('login'));
    }

    public function testUserStoreWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.user.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUserStoreValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.user.store'), [
            'name' => '',
            'email' => 'dasdfd'
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'roles']);
    }

    public function testUserStore()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $this->get(route('admin.user.index'));

        $response = $this->post(route('admin.user.store'), [
            'name' => 'Bungo',
            'email' => 'dasd@fdfdfdf.pl',
            'roles' => ['user', 'admin'],
            'password' => 'bungoslaw',
            'password_confirmation' => 'bungoslaw'
        ]);

        $response->assertOk()->assertJsonStructure(['success']);
        $response->assertSessionHas('success');

        $us = User::where([
            ['name', 'Bungo'],
            ['email', 'dasd@fdfdfdf.pl']
        ])->first();

        $this->assertTrue($us->exists());

        $this->assertDatabaseHas('roles_models', [
            'model_id' => $us->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\User',
        ]);
    }
}
