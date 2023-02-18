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
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateRoleTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateAsGuest(): void
    {
        $response = $this->get(route('admin.role.create'));

        $response->assertRedirect(route('login'));
    }

    public function testCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->superAdmin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.role.create'));

        $response->assertOk()->assertViewIs('icore::admin.role.create');
        $response->assertSee(route('admin.role.store'), false);
    }

    public function testStoreAsGuest(): void
    {
        $response = $this->post(route('admin.role.store'));

        $response->assertRedirect(route('login'));
    }

    public function testStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.role.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
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

    public function testStore(): void
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
