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

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Mail;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\Models\NewsletterToken;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use N1ebieski\ICore\Mail\Newsletter\ConfirmationMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewsletterTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewsletterStoreValidationFail(): void
    {
        /** @var Newsletter */
        $newsletter = Newsletter::makeFactory()->active()->create();

        $response1 = $this->post(route('web.newsletter.store'), [
            'email' => '',
        ]);

        $response2 = $this->post(route('web.newsletter.store'), [
            'email' => $newsletter->email,
        ]);

        $response1->assertSessionHasErrors(['email']);
        $response2->assertSessionHasErrors(['email']);
    }

    public function testNewsletterStore(): void
    {
        $response = $this->post(route('web.newsletter.store'), [
            'email' => Faker::create()->unique()->safeEmail,
            'marketing_agreement' => true
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::newsletter.success.store')
        ]);
    }

    public function testNewsletterStoreExistStatus0(): void
    {
        Mail::fake();

        /** @var Newsletter */
        $newsletter = Newsletter::makeFactory()->inactive()->create();

        Mail::assertNothingSent();

        $response = $this->post(route('web.newsletter.store'), [
            'email' => $newsletter->email,
            'marketing_agreement' => true
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::newsletter.success.store')
        ]);

        Mail::assertSent(ConfirmationMail::class, function (ConfirmationMail $mail) use ($newsletter) {
            $mail->build();

            $this->assertEquals(route('web.newsletter.update_status', [
                $newsletter->id,
                'token' => $newsletter->token->token,
                'status' => Status::ACTIVE
            ]), $mail->viewData['actionUrl']);

            return $mail->hasTo($newsletter->email);
        });
    }

    public function testNewsletterUpdateStatusWithInvalidToken(): void
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

    public function testNewsletterUpdateStatusWithExpiredToken(): void
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

    public function testNewsletterUpdateStatus1(): void
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

    public function testNewsletterUpdateStatus0(): void
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
