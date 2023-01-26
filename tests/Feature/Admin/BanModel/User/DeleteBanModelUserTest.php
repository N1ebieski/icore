<?php

namespace N1ebieski\ICore\Tests\Feature\Admin\BanModel\User;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\BanModel\BanModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteBanModelUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.banmodel.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testDestroyWithoutPermission(): void
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

    public function testNoexistDestroy(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDestroy(): void
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

    public function testDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testDestroyGlobalWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.banmodel.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDestroyGlobalValidationFail(): void
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

    public function testDestroyGlobal(): void
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
