<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\BanModel\BanModel;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BanModelTest extends TestCase
{
    use DatabaseTransactions;

    public function testBanmodelUserCreateAsGuest(): void
    {
        $response = $this->get(route('admin.banmodel.user.create', [23]));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelUserCreateWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanmodelNoexistUserCreate(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.create', [312312]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanmodelUserCreate(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.create', [$user2->id]));

        $response->assertOk()->assertJsonStructure(['view']);

        /**
         * @var JsonResponse
         */
        $baseResponse = $response->baseResponse;

        $this->assertStringContainsString($user2->name, $baseResponse->getData()->view);
        $this->assertStringContainsString(
            route('admin.banmodel.user.store', [$user2->id]),
            $baseResponse->getData()->view
        );
    }

    public function testBanmodelUserStoreAsGuest(): void
    {
        $response = $this->post(route('admin.banmodel.user.store', [99]), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelNoexistUserStoreStore(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [9999]), []);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanmodelUserStoreWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanmodelUserStoreValidationFail(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->banUser()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), [
            'user' => $user2->id,
        ]);

        $response->assertSessionHasErrors(['user']);
    }

    public function testBanmodelUserStore(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.banmodel.user.store', [$user2->id]), [
            'user' => $user2->id,
            'ip' => $user2->ip
        ]);

        $response->assertOk()->assertJson(['success' => trans('icore::bans.model.success.store')]);

        $this->assertDatabaseHas('bans_models', [
            'model_id' => $user2->id,
            'model_type' => $user2->getMorphClass()
        ]);

        $this->assertDatabaseHas('bans_values', [
            'value' => $user2->ip,
            'type' => Type::IP
        ]);
    }

    public function testBanmodelUserIndexAsGuest(): void
    {
        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelUserIndexWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanmodelUserIndexPaginate(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        /**
         * @var Collection<User>|array<User>
         */
        $users = User::makeFactory()->count(50)->create();

        foreach ($users as $u) {
            $u->ban()->create();
        }

        $response = $this->get(route('admin.banmodel.user.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'bans_models.created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.banmodel.user.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$users[30]->name], false);
    }

    public function testBanmodelDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.banmodel.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelDestroyWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->banUser()->create();

        Auth::login($user);

        /** @var BanModel */
        $ban = $user2->ban;

        $response = $this->delete(route('admin.banmodel.destroy', [$ban->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistBanmodelDestroy(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testBanmodelDestroy(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var User
         */
        $user2 = User::makeFactory()->banUser()->create();

        Auth::login($user);

        /** @var BanModel */
        $ban = $user2->ban;

        $this->assertDatabaseHas('bans_models', [
            'id' => $ban->id,
        ]);

        $response = $this->delete(route('admin.banmodel.destroy', [$ban->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('bans_models', [
            'id' => $ban->id,
        ]);
    }

    public function testBanmodelDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testBanmodelDestroyGlobalWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testBanmodelDestroyGlobalValidationFail(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testBanmodelDestroyGlobal(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        /**
         * @var User
         */
        $users = User::makeFactory()->count(20)->banUser()->create();

        Auth::login($user);

        $this->get(route('admin.banmodel.user.index'));

        $response = $this->delete(route('admin.banmodel.destroy_global'), [
            'select' => $users->pluck('id')->take(5)->toArray(),
        ]);

        $response->assertRedirect(route('admin.banmodel.user.index'));
        $response->assertSessionHas('success');

        $deleted = BanModel::whereIn('id', $users->pluck('id')->take(5)->toArray())->count();

        $this->assertTrue($deleted === 0);
    }
}
