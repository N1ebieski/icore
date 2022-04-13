<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    public function testRoleIndexAsGuest()
    {
        $response = $this->get(route('admin.role.index'));

        $response->assertRedirect(route('login'));
    }

    public function testRoleIndexWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.index'));

        $response->assertStatus(403);
    }

    public function testRoleIndexPaginate()
    {
        $user = User::makeFactory()->admin()->create();

        $role = Role::makeFactory()->count(50)->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.index', ['page' => 2]));

        $response->assertViewIs('icore::admin.role.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$role[30]->name], false);
    }

    public function testRoleDestroyAsGuest()
    {
        $response = $this->delete(route('admin.role.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testRoleDestroyWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(403);
    }

    public function testRoleDestroyDefault()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::whereName('user')->first();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(403);
    }

    public function testNoexistRoleDestroy()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testRoleDestroy()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);

        $this->get(route('admin.role.index'));

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertRedirect(route('admin.role.index'));

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function testRoleEditAsGuest()
    {
        $response = $this->get(route('admin.role.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testRoleEditWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(403);
    }

    public function testRoleEditDefault()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::where('name', 'super-admin')->first();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(403);
    }

    public function testNoexistRoleEdit()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function testRoleEdit()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertOk()->assertViewIs('icore::admin.role.edit');
        $response->assertSee($role->name, false);
        $response->assertSee(route('admin.role.update', [$role->id]), false);
    }

    public function testRoleUpdateAsGuest()
    {
        $response = $this->put(route('admin.role.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testRoleUpdateWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(403);
    }

    public function testRoleUpdateDefault()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::whereName('super-admin')->first();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(403);
    }

    public function testNoexistRoleUpdate()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [2327382]));

        $response->assertStatus(404);
    }

    public function testUserUpdateDefaultUserValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::where('name', 'user')->first();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'user',
            'perm' => [3627 => 'admin.access']
        ]);

        $response->assertSessionHasErrors(['perm.3627']);
    }

    public function testRoleUpdateValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'user',
            'perm' => [467 => 'dsad sadasdasda']
        ]);

        $response->assertSessionHasErrors(['name', 'perm.467']);
    }

    public function testRoleUpdate()
    {
        $user = User::makeFactory()->superAdmin()->create();

        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'Bungo',
            'perm' => [
                2 => 'admin.access',
                4 => 'admin.home.view'
            ]
        ]);

        $response->assertRedirect(route('admin.role.edit', [$role->id]));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Bungo'
        ]);

        $this->assertDatabaseHas('roles_permissions', [
            'role_id' => $role->id,
            'permission_id' => 2
        ]);
    }

    public function testRoleCreateAsGuest()
    {
        $response = $this->get(route('admin.role.create'));

        $response->assertRedirect(route('login'));
    }

    public function testRoleCreateWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.create'));

        $response->assertStatus(403);
    }

    public function testRoleCreate()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.create'));

        $response->assertOk()->assertViewIs('icore::admin.role.create');
        $response->assertSee(route('admin.role.store'), false);
    }

    public function testRoleStoreAsGuest()
    {
        $response = $this->post(route('admin.role.store'));

        $response->assertRedirect(route('login'));
    }

    public function testRoleStoreWithoutPermission()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'));

        $response->assertStatus(403);
    }

    public function testRoleStoreValidationFail()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'user',
            'perm' => [467 => 'dsad sadasdasda']
        ]);

        $response->assertSessionHasErrors(['name', 'perm.467']);
    }

    public function testRoleStore()
    {
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'Bungo',
            'perm' => [
                2 => 'admin.access',
                4 => 'admin.home.view'
            ]
        ]);

        $response->assertRedirect(route('admin.role.index'));
        $response->assertSessionHas('success');

        $role = Role::whereName('Bungo')->first();

        $this->assertTrue($role->exists());

        $this->assertDatabaseHas('roles_permissions', [
            'role_id' => $role->id,
            'permission_id' => 2
        ]);
    }
}
