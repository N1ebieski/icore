<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileManagerTest extends TestCase
{
    use DatabaseTransactions;

    public function testFilemanagerReadPublicDiskAsGuest()
    {
        $response = $this->getJson(route('fm.content', ['disk' => 'public']));

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testFilemanagerWritePublicDiskAsGuest()
    {
        Storage::fake('public');

        $response = $this->postJson(route('fm.upload', [
                'disk' => 'public',
                'path' => '',
                'overwrite' => 1,
                'files' => [
                    UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
                ]
            ]));

        Storage::disk('public')->assertMissing('avatar.jpg');

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testFilemanagerReadPublicDiskAsUser()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->getJson(route('fm.content', ['disk' => 'public']));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testFilemanagerWritePublicDiskAsUser()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        Storage::fake('public');

        $response = $this->postJson(route('fm.upload', [
                'disk' => 'public',
                'path' => '',
                'overwrite' => 1,
                'files' => [
                    UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
                ]
            ]));

        Storage::disk('public')->assertMissing('avatar.jpg');

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testFilemanagerWritePublicDiskAsAdmin()
    {
        $user = User::makeFactory()->admin()->create();

        Auth::login($user);

        Storage::fake('public');

        $response = $this->postJson(route('fm.upload'), [
                'disk' => 'public',
                'path' => '',
                'overwrite' => 1,
                'files' => [
                    UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
                ]
            ]);


        Storage::disk('public')->assertExists('avatar.jpg');

        $response->assertStatus(200);
    }

    public function testFilemanagerWritePublicVendorDiskAsSuperadmin()
    {
        $user = User::makeFactory()->superadmin()->create();

        Auth::login($user);

        Storage::fake('public');

        $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post(route('fm.upload'), [
                'disk' => 'public',
                'path' => 'vendor',
                'overwrite' => 1,
                'files' => [
                    UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
                ]
            ]);


        Storage::disk('public')->assertMissing('avatar.jpg');

        $response->assertOk()->assertJson(['result' => ['status' => 'error']]);
    }
}
