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

namespace N1ebieski\ICore\Tests\Feature\Admin\BanValue;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Auth;
use N1ebieski\ICore\Models\BanValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\BanValue\Type;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexBanValueTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexAsGuest(): void
    {
        $response = $this->get(route('admin.banvalue.index', [Type::IP]));

        $response->assertRedirect(route('login'));
    }

    public function testIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.banvalue.index', [Type::IP]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        /** @var Collection<BanValue>|array<BanValue> */
        $banvalue = BanValue::makeFactory()->count(50)
            ->sequence(function (Sequence $sequence) {
                return [
                    'created_at' => Carbon::now()->addSeconds($sequence->index)
                ];
            })
            ->ip()
            ->create();

        $response = $this->get(route('admin.banvalue.index', [
            'type' => Type::IP,
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.banvalue.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$banvalue[30]->value], false);
    }
}
