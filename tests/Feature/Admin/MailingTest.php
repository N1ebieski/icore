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
// use App\Jobs\SendMailing;
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
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.index'));

        $response->assertStatus(403);
    }

    public function testMailingIndexPaginate()
    {
        $user = factory(User::class)->states('admin')->create();

        $mailing = factory(Mailing::class, 50)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.index', ['page' => 2, 'orderby' => 'created_at|asc']));

        $response->assertViewIs('icore::admin.mailing.index');
        $response->assertSee('class="pagination"');
        $response->assertSeeInOrder([$mailing[30]->title, $mailing[30]->shortContent]);
    }

    public function testMailingEditAsGuest()
    {
        $response = $this->get(route('admin.mailing.edit', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingEditWithoutPermission()
    {
        $user = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingEdit()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.edit', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingEdit()
    {
        $user = factory(User::class)->states('admin')->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.edit', [$mailing->id]));

        $response->assertOk()->assertViewIs('icore::admin.mailing.edit');
        $response->assertSeeInOrder([$mailing->title, $mailing->content]);
        $response->assertSee(route('admin.mailing.update', [$mailing->id]));
    }

    public function testMailingUpdateAsGuest()
    {
        $response = $this->put(route('admin.mailing.update', [43]));

        $response->assertRedirect(route('login'));
    }

    public function testMailingUpdateWithoutPermission()
    {
        $user = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.mailing.update', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingUpdate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->put(route('admin.mailing.update', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingUpdateValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->states(['marketing', 'active'])->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingUpdateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.mailing.update_status', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingUpdateStatusValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->patch(route('admin.mailing.update_status', [$mailing->id]), [
            'status' => 323,
        ]);

        $response->assertSessionHasErrors(['status']);

        $this->assertTrue(Auth::check());
    }

    public function testMailingUpdateStatus()
    {
        $user = factory(User::class)->states('admin')->create();

        $mailing = factory(Mailing::class)->states('active')->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.mailing.destroy', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingDestroy()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.mailing.destroy', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingDestroy()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();
        $email = factory(MailingEmail::class)->states('with_user')->make();
        $email->mailing()->associate($mailing)->save();

        Auth::login($user, true);

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
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.mailing.destroy_global'), []);

        $response->assertStatus(403);
    }

    public function testMailingDestroyGlobalValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.mailing.destroy_global', [
            'dasdas',
            'dada'
        ]));

        $response->assertSessionHasErrors(['select']);
    }

    public function testMailingDestroyGlobal()
    {
        $user = factory(User::class)->states('admin')->create();

        $mailing = factory(Mailing::class, 10)->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertStatus(403);
    }

    public function testMailingCreate()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->get(route('admin.mailing.create'));

        $response->assertOk()->assertViewIs('icore::admin.mailing.create');
        $response->assertSee(route('admin.mailing.store'));
    }

    public function testMailingStoreAsGuest()
    {
        $response = $this->post(route('admin.mailing.store'));

        $response->assertRedirect(route('login'));
    }

    public function testMailingStoreWithoutPermission()
    {
        $user = factory(User::class)->create();

        Auth::login($user, true);

        $response = $this->post(route('admin.mailing.store'));

        $response->assertStatus(403);
    }

    public function testMailingStoreValidationFail()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

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
        $user = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.mailing.reset', [$mailing->id]));

        $response->assertStatus(403);
    }

    public function testNoexistMailingReset()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        $response = $this->delete(route('admin.mailing.reset', [2327382]));

        $response->assertStatus(404);
    }

    public function testMailingReset()
    {
        $user = factory(User::class)->states('admin')->create();

        $user2 = factory(User::class)->create();

        $mailing = factory(Mailing::class)->create();
        $email = factory(MailingEmail::class)->states('with_user')->make();
        $email->mailing()->associate($mailing)->save();

        Auth::login($user, true);

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
    //     $mailing = factory(Mailing::class)->make();
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
        $mailing = factory(Mailing::class)->states('active')->create();

        $email = factory(MailingEmail::class)->states('with_email')->make();
        $email->mailing()->associate($mailing)->save();

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
