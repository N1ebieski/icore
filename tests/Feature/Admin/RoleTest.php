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
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    public function testRoleIndexAsGuest(): void
    {
        $response = $this->get(route('admin.role.index'));

        $response->assertRedirect(route('login'));
    }

    public function testRoleIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRoleIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var array<Role> */
        $roles = Role::makeFactory()->count(50)->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.index', ['page' => 2]));

        $response->assertViewIs('icore::admin.role.index')
            ->assertSee('class="pagination"', false)
            ->assertSeeInOrder([$roles[30]->name], false);
    }

    public function testRoleDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.role.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testRoleDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRoleDestroyDefault(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::whereName('user')->first();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistRoleDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.role.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testRoleDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
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

    public function testRoleEditAsGuest(): void
    {
        $response = $this->get(route('admin.role.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testRoleEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRoleEditDefault(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::where('name', Name::SUPER_ADMIN)->first();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistRoleEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testRoleEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertOk()
            ->assertViewIs('icore::admin.role.edit')
            ->assertSee($role->name, false)
            ->assertSee(route('admin.role.update', [$role->id]), false);
    }

    public function testRoleUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.role.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testRoleUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRoleUpdateDefault(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::where('name', Name::SUPER_ADMIN)->first();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistRoleUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUserUpdateDefaultUserValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::where('name', 'user')->first();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'user',
            'perm' => [3627 => 'admin.access']
        ]);

        $response->assertSessionHasErrors(['perm.3627']);
    }

    public function testRoleUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]), [
            'name' => 'user',
            'perm' => [467 => 'dsad sadasdasda']
        ]);

        $response->assertSessionHasErrors(['name', 'perm.467']);
    }

    public function testRoleUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
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

    public function testRoleCreateAsGuest(): void
    {
        $response = $this->get(route('admin.role.create'));

        $response->assertRedirect(route('login'));
    }

    public function testRoleCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRoleCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.create'));

        $response->assertOk()->assertViewIs('icore::admin.role.create');
        $response->assertSee(route('admin.role.store'), false);
    }

    public function testRoleStoreAsGuest(): void
    {
        $response = $this->post(route('admin.role.store'));

        $response->assertRedirect(route('login'));
    }

    public function testRoleStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testRoleStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'user',
            'perm' => [467 => 'dsad sadasdasda']
        ]);

        $response->assertSessionHasErrors(['name', 'perm.467']);
    }

    public function testRoleStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'Bungo',
            'perm' => [
                2 => 'admin.access',
                4 => 'admin.home.view'
            ]
        ]);

        $response->assertRedirect(route('admin.role.index'))->assertSessionHas('success');

        /** @var Role */
        $role = Role::where('name', 'Bungo')->first();

        $this->assertTrue($role->exists());

        $this->assertDatabaseHas('roles_permissions', [
            'role_id' => $role->id,
            'permission_id' => 2
        ]);
    }
}
