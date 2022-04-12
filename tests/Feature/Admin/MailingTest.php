<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Crons\MailingCron;
use Illuminate\Support\Facades\Artisan;
use N1ebieski\ICore\Models\MailingEmail;
use N1ebieski\ICore\Mail\Mailing\Mail as MailingMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MailingTest extends TestCase
{
    use DatabaseTransactions;

    public function testMailingIndexAsGuest()
    {
        $response = $this->get(route('admin.mailing.index'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingIndexWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.index'));

        $response->assertStatus(403);
    }

    public function testMailingIndexPaginate()
    {
        $user = User::factory()->admin()->create();

        $mailing = Mailing::factory()->count(50)->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.index', [
            'page' => 2,
            'filter' => [
                'orderby' => 'created_at|asc'
            ]
        ]));

        $response->assertViewIs('icore::admin.mailing.index');
        $response->assertSee('class="pagination"', false);
        $response->assertSeeInOrder([$mailing[30]->title, $mailing[30]->shortContent], false);
    }

    public function testMailingEditAsGuest()
    {
        $response = $this->get(route('admin.mailing.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingEditWithoutPermission()
    {
        $user = User::factory()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingEdit()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingEdit()
    {
        $user = User::factory()->admin()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertOk()->assertViewIs('icore::admin.mailing.edit');
        $response->assertSeeInOrder([$mailing->title, $mailing->content], false);
        $response->assertSee(route('admin.mailing.update', [$mailing->id]), false);
    }

    public function testMailingUpdateAsGuest()
    {
        $response = $this->put(route('admin.mailing.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingUpdateWithoutPermission()
    {
        $user = User::factory()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingUpdate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingUpdateValidationFail()
    {
        $user = User::factory()->admin()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]), [
            'title' => '',
            'status' => 2,
            'content_html' => 'Ten mailing został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title', 'date_activation_at', 'time_activation_at']);

        $this->assertTrue(Auth::check());
    }

    public function testMailingUpdate()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->marketing()->active()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]), [
            'title' => 'Mailing zaktualizowany.',
            'content_html' => 'Ten mailing został zaktualizowany.',
            'status' => 1,
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

        $this->assertTrue(Auth::check());
    }

    public function testMailingUpdateStatusAsGuest()
    {
        $response = $this->patch(route('admin.mailing.update_status', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingUpdateStatusWithoutPermission()
    {
        $user = User::factory()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingUpdateStatusValidationFail()
    {
        $user = User::factory()->admin()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testMailingUpdateStatus()
    {
        $user = User::factory()->admin()->create();

        $mailing = Mailing::factory()->active()->create();

        Auth::login($user);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => 0,
        ]);

        $response->assertOk()->assertJsonStructure(['success', 'view']);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
            'status' => 0,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testMailingDestroyAsGuest()
    {
        $response = $this->delete(route('admin.mailing.destroy', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingDestroyWithoutPermission()
    {
        $user = User::factory()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingDestroy()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingDestroy()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->create();

        $mailing = Mailing::factory()->create();

        $email = MailingEmail::factory()->withUser()->for($mailing)->create();

        Auth::login($user);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
        ]);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseMissing('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseMissing('mailings_emails', [
            'id' => $email->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testMailingDestroyGlobalAsGuest()
    {
        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertRedirect(route('login'));
    }

    public function testMailingDestroyGlobalWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testMailingDestroyGlobalValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testMailingDestroyGlobal()
    {
        $user = User::factory()->admin()->create();

        $mailing = Mailing::factory()->count(10)->create();

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

        $this->assertTrue(Auth::check());
    }

    public function testMailingCreateAsGuest()
    {
        $response = $this->get(route('admin.mailing.create'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingCreateWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertStatus(403);
    }

    public function testMailingCreate()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertOk()->assertViewIs('icore::admin.mailing.create');
        $response->assertSee(route('admin.mailing.store'), false);
    }

    public function testMailingStoreAsGuest()
    {
        $response = $this->post(route('admin.mailing.store'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingStoreWithoutPermission()
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'));

        $response->assertStatus(403);
    }

    public function testMailingStoreValidationFail()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'), [
            'title' => '',
            'emails' => 'true',
            'status' => 0,
            'emails_json' => 'dasdad',
            'content_html' => 'Ten post został zaktualizowany.'
        ]);

        $response->assertSessionHasErrors(['title', 'emails_json']);

        $this->assertTrue(Auth::check());
    }

    public function testMailingStore()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->post(route('admin.mailing.store'), [
            'title' => 'Mailing dodany.',
            'content_html' => 'Ten mailing został dodany.',
            'emails' => 'true',
            'status' => 0,
            'emails_json' => '[{"email": "dasds@dsdada.pl"}]'
        ]);

        $response->assertRedirect(route('admin.mailing.index'));
        $response->assertSessionHas('success');

        $mailing = Mailing::where([
            ['content', 'Ten mailing został dodany.'],
            ['title', 'Mailing dodany.']
        ])->first();

        $this->assertTrue($mailing->exists());

        $this->assertDatabaseHas('mailings_emails', [
            'email' => 'dasds@dsdada.pl',
            'model_id' => null,
            'model_type' => null
        ]);

        $this->assertTrue(Auth::check());
    }

    public function testMailingResetAsGuest()
    {
        $response = $this->delete(route('admin.mailing.reset', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingResetWithoutPermission()
    {
        $user = User::factory()->create();

        $mailing = Mailing::factory()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingReset()
    {
        $user = User::factory()->admin()->create();

        Auth::login($user);

        $response = $this->delete(route('admin.mailing.reset', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingReset()
    {
        $user = User::factory()->admin()->create();

        $user2 = User::factory()->create();

        $mailing = Mailing::factory()->create();

        $email = MailingEmail::factory()->withUser()->for($mailing)->create();

        Auth::login($user);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
        ]);

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]), []);

        $response->assertOk()->assertJsonStructure(['success']);

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
        ]);

        $this->assertDatabaseMissing('mailings_emails', [
            'id' => $email->id,
        ]);

        $this->assertTrue(Auth::check());
    }

    // public function testMailingQueueFailJob()
    // {
    //     config(['queue.default' => 'database']);
    //
    //     $mailing = Mailing::factory()->make();
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

    public function testMailingQueueJob()
    {
        $mailing = Mailing::factory()->active()->create();

        $email = MailingEmail::factory()->email()->for($mailing)->create();

        Mail::fake();
        Config::set('queue.default', 'database');

        $this->assertDatabaseHas('mailings_emails', [
            'id' => $email->id,
            'sent' => 0
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
            'sent' => 1
        ]);

        $schedule = app()->make(MailingCron::class);
        $schedule();

        $this->assertDatabaseHas('mailings', [
            'id' => $mailing->id,
            'status' => 0
        ]);
    }
}
