<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.user.index'));

        $response->assertStatus(403);
    }

    public function testUserIndexPaginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $us = factory(User::class, 50)->states('active')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.user.index', [
            'page' => 2,
            'orderby' => 'created_at|desc'
        ]));

        $response->assertViewIs('icore::admin.user.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$us[30]->name, $us[30]->email]);
    }

    public function testUserUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.user.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserUpdateStatusWithoutPermission()
    {
        $user = factory(User::class)->states('admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.user.update_status', [$us->id]));

        $response->assertStatus(403);
    }

    public function testNoexistUserUpdateStatus()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.user.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testUserUpdateStatusSelf()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.user.update_status', [$user->id]), [
            'status' => 0,
        ]);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testUserUpdateStatusValidationFail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.user.update_status', [$us->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testUserUpdateStatus()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.user.update_status', [$us->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
            'status' => 0,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testUserDestroyAsGuest()
    {
        $response = $this->delete(route('admin.user.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserDestroyWithoutPermission()
    {
        $user = factory(User::class)->states('admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.user.destroy', [$us->id]));

        $response->assertStatus(403);
    }

    public function testUserDestroySelf()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.user.destroy', [$user->id]));

        $response->assertStatus(403);
    }

    public function testNoexistUserDestroy()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.user.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testUserDestroy()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $this->assertDatabaseHas('users', [
            'id' => $us->id,
        ]);

        $response = $this->delete(route('admin.user.destroy', [$us->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('users', [
            'id' => $us->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testUserDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testUserDestroyGlobalWithoutPermission()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.user.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testUserDestroyGlobalValidationFail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.user.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testUserDestroyGlobalWithSelf()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class, 10)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $this->get(route('admin.user.index'));

        $response = $this->delete(route('admin.user.destroy_global'), [
            'select' => $us->push($user)->pluck('id')->toArray(),
        ]);

        $response->assertStatus(403);
    }

    public function testUserDestroyGlobal()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class, 10)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $this->get(route('admin.user.index'));

        $select = collect($us)->pluck('id')->toArray();

        $response = $this->delete(route('admin.user.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $deleted = Post::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);

        $this->assertTrue(Auth::check());
    }

    public function testUserEditAsGuest()
    {
        $response = $this->get(route('admin.user.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUserEditWithoutPermission()
    {
        $user = factory(User::class)->states('admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.user.edit', [$us->id]));

        $response->assertStatus(403);
    }

    public function testUserEditSelf()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.user.edit', [$user->id]));

        $response->assertStatus(403);
    }

    public function testNoexistUserEdit()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.post.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function testUserEdit()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->states('admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.user.update', [$us->id]));

        $response->assertStatus(403);
    }

    public function testUserUpdateSelf()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.user.update', [$user->id]));

        $response->assertStatus(403);
    }

    public function testNoexistUserUpdate()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.user.update', [2327382]));

        $response->assertStatus(404);
    }

    public function testUserUpdateValidationFail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.user.update', [$us->id]), [
            'name' => '',
            'email' => 'dasdad@'
        ]);

        $response->assertSessionHasErrors(['name', 'email']);

        $this->assertTrue(Auth::check());
    }

    public function testUserUpdate()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $us = factory(User::class)->states(['active', 'user'])->create();

        Auth::login($user, true);

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

        $this->assertTrue(Auth::check());
    }

    public function testUserCreateAsGuest()
    {
        $response = $this->get(route('admin.user.create'));

        $response->assertRedirect(route('login'));
    }

    public function testUserCreateWithoutPermission()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.user.create'));

        $response->assertStatus(403);
    }

    public function testUserCreate()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.user.store'));

        $response->assertStatus(403);
    }

    public function testUserStoreValidationFail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.user.store'), [
            'name' => '',
            'email' => 'dasdfd'
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'roles']);

        $this->assertTrue(Auth::check());
    }

    public function testUserStore()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

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

        $this->assertTrue(Auth::check());
    }
}
