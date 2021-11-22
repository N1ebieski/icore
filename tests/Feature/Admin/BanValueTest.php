<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
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
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertStatus(403);
    }

    public function testBanvalueNoexistTypeCreate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.create', ['dasdad']));

        $response->assertSessionHasErrors(['type']);
    }

    public function testBanvalueCreate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banvalue.store', ['dasdada']), []);

        $response->assertSessionHasErrors(['type']);
    }

    public function testBanmodelUserStoreWithoutPermission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banvalue.store', ['ip']), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueStoreValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        $banmodel = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banvalue.store', ['ip']), [
            'value' => $banmodel->value,
        ]);

        $response->assertSessionHasErrors(['value']);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueStore()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banvalue.store', ['ip']), [
            'value' => '22.222.22.22',
        ]);

        $response->assertOk()->assertJsonStructure(['success']);
        $response->assertSessionHas(['success' => trans('icore::bans.value.success.store')]);

        $this->assertDatabaseHas('bans_values', [
            'value' => '22.222.22.22',
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueIndexAsGuest()
    {
        $response = $this->get(route('admin.banvalue.index', ['ip']));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueIndexWithoutPermission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.index', ['ip']));

        $response->assertStatus(403);
    }

    public function testBanvalueIndexPaginate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $banvalue = factory(BanValue::class, 50)->states('ip')->create();

        $response = $this->get(route('admin.banvalue.index', [
            'type' => 'ip',
            'page' => 2,
            'orderby' => 'created_at|desc'
        ]));

        $response->assertViewIs('icore::admin.banvalue.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$banvalue[30]->ip]);
    }

    public function testBanvalueDestroyAsGuest()
    {
        $response = $this->delete(route('admin.banvalue.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueDestroyWithoutPermission()
    {
        $user = factory(User::class)->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertStatus(403);
    }

    public function testNoexistBanvalueDestroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testBanvalueDestroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
        ]);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('bans_values', [
            'id' => $banvalue->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanvalueDestroyGlobalWithoutPermission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testBanvalueDestroyGlobalValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testBanvalueDestroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class, 20)->states('ip')->create();

        Auth::login($user, true);

        $this->get(route('admin.banvalue.index', ['ip']));

        $select = collect($banvalue)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.banvalue.destroy_global'), [
            'select' => $select,
        ]);

        $response->assertRedirect(route('admin.banvalue.index', ['ip']));
        $response->assertSessionHas('success');

        $deleted = BanValue::whereIn('id', $select)->count();

        $this->assertTrue($deleted === 0);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueEditAsGuest()
    {
        $response = $this->get(route('admin.banvalue.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistBanvalueEdit()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueEditWithoutPermission()
    {
        $user = factory(User::class)->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueEdit()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString(route('admin.banvalue.update', [$banvalue->id]), $response->getData()->view);
        $this->assertStringContainsString($banvalue->value, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueUpdateAsGuest()
    {
        $response = $this->put(route('admin.banvalue.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testNoexistBanvalueUpdate()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueUpdateWithoutPermission()
    {
        $user = factory(User::class)->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testCommentUpdateValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => ''
        ]);

        $response->assertSessionHasErrors(['value']);

        $this->assertTrue(Auth::check());
    }

    public function testBanvalueUpdate()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();
        $new_ip = '32.343.54.232';

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => $new_ip
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($new_ip, $response->getData()->view);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
            'value' => $new_ip
        ]);

        $this->assertTrue(Auth::check());
    }
}
