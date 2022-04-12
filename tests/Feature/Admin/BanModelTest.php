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
        $user = User::factory()->create();

        $user2 = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertStatus(403);
    }

    public function testBanmodelNoexistUserCreate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.create', [312312]));

        $response->assertStatus(404);
    }

    public function testBanmodelUserCreate()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->create();

        Auth::login($user);

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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [9999]), []);

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testBanmodelUserStoreWithoutPermission()
    {
        $user = User::factory()->create();

        $user2 = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), []);

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testBanmodelUserStoreValidationFail()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->banUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), [
            'user' => $user2->id,
        ]);

        $response->assertSessionHasErrors(['user']);

        $this->assertTrue(Auth::check());
    }

    public function testBanmodelUserStore()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->create();

        Auth::login($user);

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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertStatus(403);
    }

    public function testBanmodelUserIndexPaginate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $users = User::factory()->count(50)->create();

        foreach ($users as $u) {
            $u->ban()->create();
        }

        $response = $this->get(route('admin.banmodel.user.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'bans_models.created_at|desc'
            ]
        ]));

        $response->assertViewIs('icore::admin.banmodel.user.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$users[30]->name], false);
    }

    public function testBanmodelDestroyAsGuest()
    {
        $response = $this->delete(route('admin.banmodel.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelDestroyWithoutPermission()
    {
        $user = User::factory()->create();

        $user2 = User::factory()->banUser()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy', [$user2->ban->id]));

        $response->assertStatus(403);
    }

    public function testNoexistBanmodelDestroy()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testBanmodelDestroy()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->banUser()->create();

        Auth::login($user);

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
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testBanmodelDestroyGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testBanmodelDestroyGlobal()
    {
        $user = User::factory()->admin()->create();

        $users = User::factory()->count(20)->banUser()->create();

        Auth::login($user);

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
