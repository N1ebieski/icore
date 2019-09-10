<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_role_index_as_guest()
    {
        $response = $this->get(route('admin.role.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_role_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.index'));

        $response->assertStatus(403);
    }

    public function test_role_index_paginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $role = factory(Role::class, 50)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.index', ['page' => 2]));

        $response->assertViewIs('icore::admin.role.index');
        $response->assertSee('role="navigation"');
        $response->assertSeeInOrder([$role[30]->name]);
    }

    public function test_role_destroy_as_guest()
    {
        $response = $this->delete(route('admin.role.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_role_destroy_without_permission()
    {
        $user = factory(User::class)->states('admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(403);
    }

    public function test_role_destroy_default()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = Role::whereName('user')->first();

        Auth::login($user, true);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_role_destroy()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.role.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_role_destroy()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);

        $this->get(route('admin.role.index'));

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertRedirect(route('admin.role.index'));

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_role_edit_as_guest()
    {
        $response = $this->get(route('admin.role.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_role_edit_without_permission()
    {
        $user = factory(User::class)->states('admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(403);
    }

    public function test_role_edit_default()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = Role::whereName('super-admin')->first();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_role_edit()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function test_role_edit()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertOk()->assertViewIs('icore::admin.role.edit');
        $response->assertSee($role->name);
        $response->assertSee(route('admin.role.update', [$role->id]));

    }

    public function test_role_update_as_guest()
    {
        $response = $this->put(route('admin.role.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_role_update_without_permission()
    {
        $user = factory(User::class)->states('admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(403);
    }

    public function test_role_update_default()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = Role::whereName('super-admin')->first();

        Auth::login($user, true);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_role_update()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.role.update', [2327382]));

        $response->assertStatus(404);
    }

    public function test_user_update_default_user_validation_fail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = Role::whereName('user')->first();

        Auth::login($user, true);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'user',
            'perm' => [3627 => 'access admin']
        ]);

        $response->assertSessionHasErrors(['perm.3627']);

        $this->assertTrue(Auth::check());
    }

    public function test_role_update_validation_fail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'user',
            'perm' => [467 => 'dsad sadasdasda']
        ]);

        $response->assertSessionHasErrors(['name', 'perm.467']);

        $this->assertTrue(Auth::check());
    }

    public function test_role_update()
    {
        $user = factory(User::class)->states('super-admin')->create();

        $role = factory(Role::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'Bungo',
            'perm' => [
                1 => 'access admin',
                2 => 'index dashboard'
            ]
        ]);

        $response->assertRedirect(route('admin.role.edit', [$role->id]));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Bungo'
        ]);

        $this->assertDatabaseHas('roles_permissions', [
            'role_id' => $role->id,
            'permission_id' => 1
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_role_create_as_guest()
    {
        $response = $this->get(route('admin.role.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_role_create_without_permission()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.create'));

        $response->assertStatus(403);
    }

    public function test_role_create()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.role.create'));

        $response->assertOk()->assertViewIs('icore::admin.role.create');
        $response->assertSee(route('admin.role.store'));

    }

    public function test_role_store_as_guest()
    {
        $response = $this->post(route('admin.role.store'));

        $response->assertRedirect(route('login'));
    }

    public function test_role_store_without_permission()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.role.store'));

        $response->assertStatus(403);
    }

    public function test_role_store_validation_fail()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'user',
            'perm' => [467 => 'dsad sadasdasda']
        ]);

        $response->assertSessionHasErrors(['name', 'perm.467']);

        $this->assertTrue(Auth::check());
    }

    public function test_role_store()
    {
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'Bungo',
            'perm' => [
                1 => 'access admin',
                2 => 'index dashboard'
            ]
        ]);

        $response->assertRedirect(route('admin.role.index'));
        $response->assertSessionHas('success');

        $role = Role::whereName('Bungo')->first();

        $this->assertTrue($role->exists());

        $this->assertDatabaseHas('roles_permissions', [
            'role_id' => $role->id,
            'permission_id' => 1
        ]);

        $this->assertTrue(Auth::check());
    }

}
