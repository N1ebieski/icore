<?php

namespace N1ebieski\ICore\Tests\Feature\Admin\BanModel\User;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexBanModelUserTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexAsGuest(): void
    {
        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertRedirect(route('login'));
    }

    public function testIndexWithoutPermission(): void
    {
        /**
         * @var User
         */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banmodel.user.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testIndexPaginate(): void
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
}
