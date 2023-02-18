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

namespace N1ebieski\ICore\Tests\Feature\Web\Newsletter;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\Models\NewsletterToken;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateStatusNewsletterTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStatusWithInvalidToken(): void
    {
        /** @var Newsletter */
        $newsletter = Newsletter::makeFactory()->inactive()->create();

        NewsletterToken::makeFactory()->for($newsletter)->create();

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => 'dasdasd236d7s6d7s',
            'status' => Status::ACTIVE
        ]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertSeeText('token is invalid', false);
    }

    public function testUpdateStatusWithExpiredToken(): void
    {
        /** @var Newsletter */
        $newsletter = Newsletter::makeFactory()->inactive()->create();

        NewsletterToken::makeFactory()->for($newsletter)->create([
            'updated_at' => Carbon::now()->subMinutes(61)
        ]);

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => $newsletter->token->token,
            'status' => Status::ACTIVE
        ]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertSeeText('token period has expired', false);
    }

    public function testUpdateStatusActive(): void
    {
        /** @var Newsletter */
        $newsletter = Newsletter::makeFactory()->inactive()->create();

        NewsletterToken::makeFactory()->for($newsletter)->create();

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => $newsletter->token->token,
            'status' => Status::ACTIVE
        ]));

        $response->assertRedirect(route('web.home.index'));
        $response->assertSessionHas(['success' => trans('icore::newsletter.success.update_status.1')]);

        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'status' => Status::ACTIVE
        ]);
    }

    public function testUpdateStatusInactive(): void
    {
        /** @var Newsletter */
        $newsletter = Newsletter::makeFactory()->active()->create();

        NewsletterToken::makeFactory()->for($newsletter)->create();

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => $newsletter->token->token,
            'status' => Status::INACTIVE
        ]));

        $response->assertRedirect(route('web.home.index'));
        $response->assertSessionHas(['success' => trans('icore::newsletter.success.update_status.' . Status::INACTIVE)]);

        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'status' => Status::INACTIVE
        ]);
    }
}
