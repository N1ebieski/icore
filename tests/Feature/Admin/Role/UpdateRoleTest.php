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

namespace N1ebieski\ICore\Tests\Feature\Admin\Role;

use Tests\TestCase;
use N1ebieski\ICore\Models\Role;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\ValueObjects\Role\Name;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateRoleTest extends TestCase
{
    use DatabaseTransactions;

    public function testEditAsGuest(): void
    {
        $response = $this->get(route('admin.role.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditDefault(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::where('name', Name::SUPER_ADMIN)->first();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEditNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEditRole(): void
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

    public function testUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.role.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Role */
        $role = Role::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateDefault(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        /** @var Role */
        $role = Role::where('name', Name::SUPER_ADMIN)->first();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [$role->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdateNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.role.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdateDefaultUserValidationFail(): void
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

    public function testUpdateValidationFail(): void
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

    public function testUpdate(): void
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
}
