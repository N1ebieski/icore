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

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Crons\MailingCron;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use N1ebieski\ICore\ValueObjects\MailingEmail\Sent;
use N1ebieski\ICore\Mail\Mailing\Mail as MailingMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\ICore\Models\MailingEmail\User\MailingEmail;

class MailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testMailingIndexAsGuest(): void
    {
        $response = $this->get(route('admin.mailing.index'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingIndexWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.index'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testMailingIndexPaginate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Mailing>|array<Mailing> */
        $mailings = Mailing::makeFactory()->count(50)->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.mailing.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$mailings[30]->title, $mailings[30]->shortContent], false);
    }

    public function testMailingEditAsGuest(): void
    {
        $response = $this->get(route('admin.mailing.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingEditWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistMailingEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testMailingEdit(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertOk()
            ->assertViewIs('icore::admin.mailing.edit')
            ->assertSeeInOrder([$mailing->title, $mailing->content], false)
            ->assertSee(route('admin.mailing.update', [$mailing->id]), false);
    }

    public function testMailingUpdateAsGuest(): void
    {
        $response = $this->put(route('admin.mailing.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingUpdateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistMailingUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testMailingUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]), [
            'title' => '',
            'status' => 2,
            'content_html' => 'Ten mailing został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title', 'date_activation_at', 'time_activation_at']);
    }

    public function testMailingUpdate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $user2 = User::makeFactory()->marketing()->active()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]), [
            'title' => 'Mailing zaktualizowany.',
            'content_html' => 'Ten mailing został zaktualizowany.',
            'status' => Status::ACTIVE,
            'users' => 'true'
        ]);

        $response->assertRedirect(route('admin.mailing.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mailings', [
            'content' => 'Ten mailing został zaktualizowany.',
            'title' => 'Mailing zaktualizowany.',
            'id' => $mailing->id
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'model_id' => $user2->id,
            'model_type' => 'N1ebieski\\ICore\\Models\\User',
            'email' => $user2->email
        ]);
    }

    public function testMailingUpdateStatusAsGuest(): void
    {
        $response = $this->patch(route('admin.mailing.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingUpdateStatusWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistMailingUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testMailingUpdateStatusValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function testMailingUpdateStatus(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => Status::INACTIVE,
        ]);

        $response->assertOk()->assertJsonStructure(['view']);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
            'status' => Status::INACTIVE,
        ]);
    }

    public function testMailingDestroyAsGuest(): void
    {
        $response = $this->delete(route('admin.mailing.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingDestroyWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistMailingDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testMailingDestroy(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        /** @var MailingEmail */
        $email = MailingEmail::makeFactory()->withMorph()->for($mailing)->create();

        Auth::login($user);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
        ]);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]), []);

        $response->assertOk();

        $this->assertDatabaseMissing('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseMissing('mailings_emails', [
            'id' => $email->id,
        ]);
    }

    public function testMailingDestroyGlobalAsGuest(): void
    {
        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testMailingDestroyGlobalWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testMailingDestroyGlobalValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testMailingDestroyGlobal(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var Collection<Mailing> */
        $mailing = Mailing::makeFactory()->count(10)->create();

        Auth::login($user);

        $this->get(route('admin.mailing.index'));

        $select = collect($mailing)->pluck('id')->take(5)->toArray();

        $response = $this->delete(route('admin.mailing.destroy_global'), [
            'select' => $select
        ]);

        $response->assertRedirect(route('admin.mailing.index'));
        $response->assertSessionHas('success');

        $deleted = Mailing::whereIn('id', $select)->get();

        $this->assertTrue($deleted->count() === 0);
    }

    public function testMailingCreateAsGuest(): void
    {
        $response = $this->get(route('admin.mailing.create'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingCreateWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testMailingCreate(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertOk()
            ->assertViewIs('icore::admin.mailing.create')
            ->assertSee(route('admin.mailing.store'), false);
    }

    public function testMailingStoreAsGuest(): void
    {
        $response = $this->post(route('admin.mailing.store'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingStoreWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testMailingStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'), [
            'title' => '',
            'emails' => 'true',
            'status' => Status::INACTIVE,
            'emails_json' => '',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title', 'emails_json']);
    }

    public function testMailingStore(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'), [
            'title' => 'Mailing dodany.',
            'content_html' => 'Ten mailing został dodany.',
            'emails' => 'true',
            'status' => Status::INACTIVE,
            'emails_json' => '[{"email": "dasds@dsdada.pl"}]'
        ]);

        $response->assertSessionHas('success');

        /** @var Mailing|null */
        $mailing = Mailing::where([
            ['content', 'Ten mailing został dodany.'],
            ['title', 'Mailing dodany.']
        ])->first();

        $this->assertTrue($mailing?->exists());

        $response->assertRedirect(route('admin.mailing.index', [
            'filter' => [
                'search' => "id:\"{$mailing->id}\""
            ]
        ]));

        $this->assertDatabaseHas('mailings_emails', [
            'email' => 'dasds@dsdada.pl',
            'model_id' => null,
            'model_type' => null
        ]);
    }

    public function testMailingResetAsGuest(): void
    {
        $response = $this->delete(route('admin.mailing.reset', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingResetWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testNoexistMailingReset(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.reset', [2327382]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testMailingReset(): void
    {
        /** @var User */
        $user = User::makeFactory()->admin()->create();

        /** @var User */
        $user2 = User::makeFactory()->create();

        /** @var Mailing */
        $mailing = Mailing::makeFactory()->create();

        /** @var MailingEmail */
        $email = MailingEmail::makeFactory()->withMorph()->for($mailing)->create();

        Auth::login($user);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
        ]);

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]), []);

        $response->assertOk();

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseMissing('mailings_emails', [
            'id' => $email->id,
        ]);
    }

    // public function testMailingQueueFailJob()
    // {
    //     config(['queue.default' => 'database']);
    //
    //     $mailing = Mailing::makeFactory()->make();
    //     $mailing->status = 1;
    //     $mailing->save();
    //
    //     $email = MailingEmail::make();
    //     $email->email = '';
    //
    //     $mailing->emails()->save($email);
    //
    //     $this->assertDatabaseHas('mailings_emails', [
    //         'id' => $email->id,
    //         'sent' => 0
    //     ]);
    //
    //     $schedule = app()->make(MailingCron::class);
    //     $schedule();
    //
    //     $this->assertDatabaseHas('mailings_emails', [
    //         'id' => $email->id,
    //         'sent' => 2
    //     ]);
    //
    //     // $this->artisan('schedule:run --env=testing');
    //     //
    //     // $this->assertDatabaseHas('mailings', [
    //     //     'id' => $mailing->id,
    //     //     'status' => 0
    //     // ]);
    // }

    public function testMailingQueueJob(): void
    {
        /** @var Mailing */
        $mailing = Mailing::makeFactory()->active()->create();

        /** @var MailingEmail */
        $email = MailingEmail::makeFactory()->email()->for($mailing)->create();

        Mail::fake();
        Config::set('queue.default', 'database');

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => Sent::UNSENT
        ]);

        // Uruchamiamy zadanie crona bezpośrednio, bo przez schedule:run ma ustalony delay
        // (np. odpala się co godzinę)
        $schedule = app()->make(MailingCron::class);
        $schedule();

        Artisan::call('queue:work', ['--daemon' => true, '--tries' => 3, '--once' => true]);

        Mail::assertSent(MailingMail::class, function ($mail) use ($email, $mailing) {
            $mail->build();

            return $mail->hasTo($email->email) && $mail->subject($mailing->title);
        });

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => Sent::SENT
        ]);

        $schedule = app()->make(MailingCron::class);
        $schedule();

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
            'status' => Status::INACTIVE
        ]);
    }
}
