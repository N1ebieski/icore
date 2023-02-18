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

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Mail;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use N1ebieski\ICore\Mail\Newsletter\ConfirmationMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateNewsletterTest extends TestCase
{
    use DatabaseTransactions;

    public function testStoreValidationFail(): void
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

    public function testStore(): void
    {
        $response = $this->post(route('web.newsletter.store'), [
            'email' => Faker::create()->unique()->safeEmail,
            'marketing_agreement' => true
        ]);

        $response->assertOk()->assertJson([
            'success' => trans('icore::newsletter.success.store')
        ]);
    }

    public function testStoreExistStatus0(): void
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
}
