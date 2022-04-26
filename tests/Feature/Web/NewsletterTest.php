<?php

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

    public function testNewsletterStoreValidationFail()
    {
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

    public function testNewsletterStore()
    {
        $response = $this->post(route('web.newsletter.store'), [
            'email' => Faker::create()->unique()->safeEmail,
            'marketing_agreement' => true
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::newsletter.success.store')
        ]);
    }

    public function testNewsletterStoreExistStatus0()
    {
        Mail::fake();

        $newsletter = Newsletter::makeFactory()->inactive()->create();

        Mail::assertNothingSent();

        $response = $this->post(route('web.newsletter.store'), [
            'email' => $newsletter->email,
            'marketing_agreement' => true
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::newsletter.success.store')
        ]);

        Mail::assertSent(ConfirmationMail::class, function ($mail) use ($newsletter) {
            $mail->build();

            $this->assertEquals(route('web.newsletter.update_status', [
                $newsletter->id,
                'token' => $newsletter->token->token,
                'status' => Status::ACTIVE
            ]), $mail->viewData['actionUrl']);

            return $mail->hasTo($newsletter->email) && $mail->subject(trans('icore::newsletter.subscribe_confirmation'));
        });
    }

    public function testNewsletterUpdateStatusWithInvalidToken()
    {
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

    public function testNewsletterUpdateStatusWithExpiredToken()
    {
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

    public function testNewsletterUpdateStatus1()
    {
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

    public function testNewsletterUpdateStatus0()
    {
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
