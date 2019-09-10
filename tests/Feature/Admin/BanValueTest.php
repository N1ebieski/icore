<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class BanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function test_banvalue_create_as_guest()
    {
        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertRedirect(route('login'));
    }

    public function test_banvalue_create_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertStatus(403);
    }

    public function test_banvalue_noexist_type_create()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.create', ['dasdad']));

        $response->assertSessionHasErrors(['type']);
    }

    public function test_banvalue_create()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.create', ['ip']));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('admin.banvalue.store', ['ip']), $response->getData()->view);
    }

    public function test_banvalue_store_as_guest()
    {
        $response = $this->post(route('admin.banvalue.store', ['ip']), []);

        $response->assertRedirect(route('login'));
    }

    public function test_banvalue_noexist_type_store()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banvalue.store', ['dasdada']), []);

        $response->assertSessionHasErrors(['type']);
    }

    public function test_banmodel_user_store_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banvalue.store', ['ip']), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_banvalue_store_validation_fail()
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

    public function test_banvalue_store()
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

    public function test_banvalue_index_as_guest()
    {
        $response = $this->get(route('admin.banvalue.index', ['ip']));

        $response->assertRedirect(route('login'));
    }

    public function test_banvalue_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.index', ['ip']));

        $response->assertStatus(403);
    }

    public function test_banvalue_index_paginate()
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
        $response->assertSee('role="navigation"');
        $response->assertSeeInOrder([$banvalue[30]->ip]);
    }

    public function test_banvalue_destroy_as_guest()
    {
        $response = $this->delete(route('admin.banvalue.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_banvalue_destroy_without_permission()
    {
        $user = factory(User::class)->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy', [$banvalue->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_banvalue_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_banvalue_destroy()
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

    public function test_banvalue_destroyGlobal_as_guest()
    {
        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_banvalue_destroyGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function test_banvalue_destroyGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banvalue.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function test_banvalue_destroyGlobal()
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

    public function test_banvalue_edit_as_guest()
    {
        $response = $this->get(route('admin.banvalue.edit', [99]));

        $response->assertRedirect(route('login'));
    }

    public function test_noexist_banvalue_edit()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.edit', [9999]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_banvalue_edit_without_permission()
    {
        $user = factory(User::class)->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_banvalue_edit()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banvalue.edit', [$banvalue->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains(route('admin.banvalue.update', [$banvalue->id]), $response->getData()->view);
        $this->assertContains($banvalue->value, $response->getData()->view);

        $this->assertTrue(Auth::check());
    }

    public function test_banvalue_update_as_guest()
    {
        $response = $this->put(route('admin.banvalue.update', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_noexist_banvalue_update()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_banvalue_update_without_permission()
    {
        $user = factory(User::class)->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_comment_update_validation_fail()
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

    public function test_banvalue_update()
    {
        $user = factory(User::class)->states('admin')->create();

        $banvalue = factory(BanValue::class)->states('ip')->create();
        $new_ip = '32.343.54.232';

        Auth::login($user, true);

        $response = $this->put(route('admin.banvalue.update', [$banvalue->id]), [
            'value' => $new_ip
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains($new_ip, $response->getData()->view);

        $this->assertDatabaseHas('bans_values', [
            'id' => $banvalue->id,
            'value' => $new_ip
        ]);

        $this->assertTrue(Auth::check());
    }

}
