<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanModel\BanModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BanModelTest extends TestCase
{
    use DatabaseTransactions;

    public function testBanmodelUserCreateAsGuest()
    {
        $response = $this->get(route('admin.banmodel.user.create', [23]));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelUserCreateWithoutPermission()
    {
        $user = factory(User::class)->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertStatus(403);
    }

    public function testBanmodelNoexistUserCreate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.create', [312312]));

        $response->assertStatus(404);
    }

    public function testBanmodelUserCreate()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertOk()->assertJsonStructure(['success', 'view']);
        $this->assertStringContainsString($user2->name, $response->getData()->view);
        $this->assertStringContainsString(route('admin.banmodel.user.store', [$user2->id]), $response->getData()->view);
    }

    public function testBanmodelUserStoreAsGuest()
    {
        $response = $this->post(route('admin.banmodel.user.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelNoexistUserStoreStore()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banmodel.user.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testBanmodelUserStoreWithoutPermission()
    {
        $user = factory(User::class)->create();

        $user2 = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testBanmodelUserStoreValidationFail()
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

    public function testBanmodelUserStore()
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

    public function testBanmodelUserIndexAsGuest()
    {
        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelUserIndexWithoutPermission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertStatus(403);
    }

    public function testBanmodelUserIndexPaginate()
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

    public function testBanmodelDestroyAsGuest()
    {
        $response = $this->delete(route('admin.banmodel.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelDestroyWithoutPermission()
    {
        $user = factory(User::class)->create();

        $user2 = factory(User::class)->states('ban_user')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy', [$user2->ban->id]));

        $response->assertStatus(403);
    }

    public function testNoexistBanmodelDestroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testBanmodelDestroy()
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

    public function testBanmodelDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelDestroyGlobalWithoutPermission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testBanmodelDestroyGlobalValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.banmodel.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testBanmodelDestroyGlobal()
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
