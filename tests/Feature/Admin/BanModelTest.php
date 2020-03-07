<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\BanModel\BanModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class BanModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_banmodel_user_create_as_guest()
    {
        $response = $this->get(route('admin.banmodel.user.create', [23]));

        $response->assertRedirect(route('login'));
    }

    public function test_banmodel_user_create_without_permission()
    {
        $user = factory(User::class)->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertStatus(403);
    }

    public function test_banmodel_noexist_user_create()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.create', [312312]));

        $response->assertStatus(404);
    }

    public function test_banmodel_user_create()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertContains($user2->name, $response->getData()->view);
        $this->assertContains(route('admin.banmodel.user.store', [$user2->id]), $response->getData()->view);
    }

    public function test_banmodel_user_store_as_guest()
    {
        $response = $this->post(route('admin.banmodel.user.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_banmodel_noexist_user_store_store()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banmodel.user.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function test_banmodel_user_store_without_permission()
    {
        $user = factory(User::class)->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function test_banmodel_user_store_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->states('ban_user')->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), [
            'user' => $user2->id,
        ]);

        $response->assertSessionHasErrors(['user']);

        $this->assertTrue(Auth::check());
    }

    public function test_banmodel_user_store()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), [
            'user' => $user2->id,
            'ip' => $user2->ip
        ]);

        $response->assertOk()->assertJson(['success' => trans('icore::bans.model.success.store')]);

        $this->assertDatabaseHas('bans_models', [
            'model_id' => $user2->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\User'
        ]);

        $this->assertDatabaseHas('bans_values', [
            'value' => $user2->ip,
            'type' => 'ip'
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_banmodel_user_index_as_guest()
    {
        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_banmodel_user_index_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertStatus(403);
    }

    public function test_banmodel_user_index_paginate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $users = factory(User::class, 50)->create();

        foreach ($users as $u) {
            $u->ban()->create();
        }

        $response = $this->get(route('admin.banmodel.user.index', ['page' => 2, 'orderby' => 'bans_models.created_at|desc']));

        $response->assertViewIs('icore::admin.banmodel.user.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$users[30]->name]);
    }

    public function test_banmodel_destroy_as_guest()
    {
        $response = $this->delete(route('admin.banmodel.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function test_banmodel_destroy_without_permission()
    {
        $user = factory(User::class)->create();

        $user2 = factory(User::class)->states('ban_user')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy', [$user2->ban->id]));

        $response->assertStatus(403);
    }

    public function test_noexist_banmodel_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function test_banmodel_destroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->states('ban_user')->create();

        Auth::login($user, true);

        $this->assertDatabaseHas('bans_models', [
            'id' => $user2->ban->id,
        ]);

        $response = $this->delete(route('admin.banmodel.destroy', [$user2->ban->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('bans_models', [
            'id' => $user2->ban->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_banmodel_destroyGlobal_as_guest()
    {
        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_banmodel_destroyGlobal_without_permission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function test_banmodel_destroyGlobal_validation_fail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function test_banmodel_destroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $users = factory(User::class, 20)->states('ban_user')->create();

        Auth::login($user, true);

        $this->get(route('admin.banmodel.user.index'));

        $response = $this->delete(route('admin.banmodel.destroy_global'), [
            'select' => $users->pluck('id')->take(5)->toArray(),
        ]);

        $response->assertRedirect(route('admin.banmodel.user.index'));
        $response->assertSessionHas('success');

        $deleted = BanModel::whereIn('id', $users->pluck('id')->take(5)->toArray())->count();

        $this->assertTrue($deleted === 0);

        $this->assertTrue(Auth::check());
    }

}
