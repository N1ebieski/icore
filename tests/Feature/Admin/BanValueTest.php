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
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function testBanvalueCreateAsGuest(): void
    {
        $response = $this->get(route('admin.banvalue.create', [Type::IP]));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', [Type::IP]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueNoexistTypeCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', ['dasdad']));

        $response->assertSessionHasErrors(['type']);
    }

    public function testBanvalueCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', [Type::IP]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.banvalue.store', [Type::IP]),
            $baseResponse->getData()->view
        );
    }

    public function testBanvalueStoreAsGuest(): void
    {
        $response = $this->post(route('admin.banvalue.store', [Type::IP]), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueNoexistTypeStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', ['dasdada']), []);

        $response->assertSessionHasErrors(['type']);
    }

    public function testBanmodelUserStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', [Type::IP]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banmodel = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', [Type::IP]), [
            'value' => $banmodel->value,
        ]);

        $response->assertSessionHasErrors(['value']);
    }

    public function testBanvalueStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', [Type::IP]), [
            'value' => '22.222.22.22',
        ]);

        $response->assertOk();
        $response->assertSessionHas(['success' => trans('icore::bans.value.success.store')]);

        $this->assertDatabaseHas('bans_values', [
            'value' => '22.222.22.22',
        ]);
    }

    public function testBanvalueIndexAsGuest(): void
    {
        $response = $this->get(route('admin.banvalue.index', [Type::IP]));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.index', [Type::IP]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        /** @var Collection<BanValue>|array<BanValue> */
        $banvalue = BanValue::makeFactory()->count(50)->ip()->create();

        $response = $this->get(route('admin.banvalue.index', [
            'type' => Type::IP,
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|desc'
            ]
        ]));

        $response->assertViewIs('icore::admin.banvalue.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$banvalue[30]->value], false);
    }

    public function testBanvalueDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.banvalue.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistBanvalueDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanvalueDestroy(): void
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

    public function testBanvalueDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueDestroyGlobalValidationFail(): void
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

    public function testBanvalueDestroyGlobal(): void
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

    public function testBanvalueEditAsGuest(): void
    {
        $response = $this->get(route('admin.banvalue.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistBanvalueEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanvalueEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString(
            route('admin.banvalue.update', [$banvalue->id]),
            $baseResponse->getData()->view
        );
        $this->assertStringContainsString(
            $banvalue->value,
            $baseResponse->getData()->view
        );
    }

    public function testBanvalueUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.banvalue.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoexistBanvalueUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanvalueUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCommentUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => ''
        ]);

        $response->assertSessionHasErrors(['value']);
    }

    public function testBanvalueUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var BanValue */
        $banvalue = BanValue::makeFactory()->ip()->create();

        $new_ip = '32.343.54.232';

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => $new_ip
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['view']);

        /** @var JsonResponse */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString($new_ip, $baseResponse->getData()->view);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
            'value' => $new_ip
        ]);
    }
}
