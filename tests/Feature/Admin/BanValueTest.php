<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function testBanvalueCreateAsGuest()
    {
        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueCreateWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueNoexistTypeCreate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', ['dasdad']));

        $response->assertSessionHasErrors(['type']);
    }

    public function testBanvalueCreate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.banvalue.store', ['ip']), $response->getData()->view);
    }

    public function testBanvalueStoreAsGuest()
    {
        $response = $this->post(route('admin.banvalue.store', ['ip']), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueNoexistTypeStore()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', ['dasdada']), []);

        $response->assertSessionHasErrors(['type']);
    }

    public function testBanmodelUserStoreWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', ['ip']), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueStoreValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $banmodel = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', ['ip']), [
            'value' => $banmodel->value,
        ]);

        $response->assertSessionHasErrors(['value']);
    }

    public function testBanvalueStore()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banvalue.store', ['ip']), [
            'value' => '22.222.22.22',
        ]);

        $response->assertOk()->assertJsonStructure(['success']);
        $response->assertSessionHas(['success' => trans('icore::bans.value.success.store')]);

        $this->assertDatabaseHas('bans_values', [
            'value' => '22.222.22.22',
        ]);
    }

    public function testBanvalueIndexAsGuest()
    {
        $response = $this->get(route('admin.banvalue.index', ['ip']));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueIndexWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.index', ['ip']));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueIndexPaginate()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $banvalue = BanValue::makeFactory()->count(50)->ip()->create();

        $response = $this->get(route('admin.banvalue.index', [
            'type' => 'ip',
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|desc'
            ]
        ]));

        $response->assertViewIs('icore::admin.banvalue.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$banvalue[30]->ip], false);
    }

    public function testBanvalueDestroyAsGuest()
    {
        $response = $this->delete(route('admin.banvalue.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueDestroyWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistBanvalueDestroy()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanvalueDestroy()
    {
        $user = User::makeFactory()->admin()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
        ]);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('bans_values', [
            'id' => $banvalue->id,
        ]);
    }

    public function testBanvalueDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueDestroyGlobalWithoutPermission()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueDestroyGlobalValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banvalue.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testBanvalueDestroyGlobal()
    {
        $user = User::makeFactory()->admin()->create();

        $banvalue = BanValue::makeFactory()->count(20)->ip()->create();

        Auth::login($user);

        $this->get(route('admin.banvalue.index', ['ip']));

        $select = collect($banvalue)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.banvalue.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.banvalue.index', ['ip']));
        $response->assertSessionHas('success');

        $deleted = BanValue::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);
    }

    public function testBanvalueEditAsGuest()
    {
        $response = $this->get(route('admin.banvalue.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistBanvalueEdit()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [9999]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanvalueEditWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanvalueEdit()
    {
        $user = User::makeFactory()->admin()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.banvalue.update', [$banvalue->id]), $response->getData()->view);
        $this->assertStringContainsString($banvalue->value, $response->getData()->view);
    }

    public function testBanvalueUpdateAsGuest()
    {
        $response = $this->put(route('admin.banvalue.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoexistBanvalueUpdate()
    {
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanvalueUpdateWithoutPermission()
    {
        $user = User::makeFactory()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCommentUpdateValidationFail()
    {
        $user = User::makeFactory()->admin()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => ''
        ]);

        $response->assertSessionHasErrors(['value']);
    }

    public function testBanvalueUpdate()
    {
        $user = User::makeFactory()->admin()->create();

        $banvalue = BanValue::makeFactory()->ip()->create();
        $new_ip = '32.343.54.232';

        Auth::login($user);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => $new_ip
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($new_ip, $response->getData()->view);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
            'value' => $new_ip
        ]);
    }
}
