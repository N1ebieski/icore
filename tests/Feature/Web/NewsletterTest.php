<?php

namespace N1ebieski\ICore\Tests\Feature\Web;

use Carbon\Carbon;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\Mail\Newsletter\ConfirmationMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewsletterTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewsletterStoreValidationFail()
    {
        $newsletter = factory(Newsletter::class)->states('active')->create();

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

        $newsletter = factory(Newsletter::class)->states('inactive')->create();

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
                'status' => 1
            ]), $mail->viewData['actionUrl']);

            return $mail->hasTo($newsletter->email) && $mail->subject(trans('icore::newsletter.subscribe_confirmation'));
        });
    }

    public function testNewsletterUpdateStatusWithInvalidToken()
    {
        $newsletter = factory(Newsletter::class)->states('inactive')->create();
        $newsletter->token()->create(['token' => Str::random(30)]);

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => 'dasdasd236d7s6d7s',
            'status' => 1
        ]));

        $response->assertStatus(403);
        $response->assertSeeText('token is invalid');
    }

    public function testNewsletterUpdateStatusWithExpiredToken()
    {
        $newsletter = factory(Newsletter::class)->states('inactive')->create();
        $newsletter->token()->create([
            'token' => Str::random(30),
            'updated_at' => Carbon::now()->subMinutes(61)
        ]);

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => $newsletter->token->token,
            'status' => 1
        ]));

        $response->assertStatus(403);
        $response->assertSeeText('token period has expired');
    }

    public function testNewsletterUpdateStatus1()
    {
        $newsletter = factory(Newsletter::class)->states('inactive')->create();
        $newsletter->token()->create(['token' => Str::random(30)]);

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => $newsletter->token->token,
            'status' => 1
        ]));

        $response->assertRedirect(route('web.home.index'));
        $response->assertSessionHas(['success' => trans('icore::newsletter.success.update_status.1')]);

        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'status' => 1
        ]);
    }

    public function testNewsletterUpdateStatus0()
    {
        $newsletter = factory(Newsletter::class)->states('active')->create();
        $newsletter->token()->create(['token' => Str::random(30)]);

        $response = $this->get(route('web.newsletter.update_status', [
            $newsletter->id,
            'token' => $newsletter->token->token,
            'status' => 0
        ]));

        $response->assertRedirect(route('web.home.index'));
        $response->assertSessionHas(['success' => trans('icore::newsletter.success.update_status.' . Newsletter::INACTIVE)]);

        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'status' => 0
        ]);
    }
}
