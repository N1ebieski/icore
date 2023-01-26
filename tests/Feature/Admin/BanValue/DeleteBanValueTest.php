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

namespace N1ebieski\ICore\Tests\Feature\Admin\BanValue;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteBanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.banvalue.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyNoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
        ]);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertOk();

        $this->assertDatabaseMissing('bans_values', [
            'id' => $banvalue->id,
        ]);
    }

    public function testDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->count(20)->ip()->create();

        Auth::login($user);

        $this->get(route('admin.banvalue.index', [Type::IP]));

        $select = collect($banvalue)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.banvalue.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.banvalue.index', [Type::IP]));
        $response->assertSessionHas('success');

        $deleted = BanValue::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);
    }
}
