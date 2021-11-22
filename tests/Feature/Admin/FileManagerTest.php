<?php

namespace N1ebieski\ICore\Tests\Feature\Admin;

use Tests\TestCase;
use N1ebieski\ICore\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileManager extends TestCase
{
    use DatabaseTransactions;

    public function testFilemanagerReadPublicDiskAsGuest()
    {
        $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get(route('fm.content', ['disk' => 'public']));

        $response->assertStatus(401);
    }

    public function testFilemanagerWritePublicDiskAsGuest()
    {
        Storage::fake('public');

        $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post(route('fm.upload', [
                'disk' => 'public',
                'path' => '',
                'overwrite' => 1,
                'files' => [
                    UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
                ]
            ]));

        Storage::disk('public')->assertMissing('avatar.jpg');

        $response->assertStatus(401);
    }

    public function testFilemanagerReadPublicDiskAsUser()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get(route('fm.content', ['disk' => 'public']));

        $response->assertStatus(403);
    }

    public function testFilemanagerWritePublicDiskAsUser()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        Storage::fake('public');

        $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post(route('fm.upload', [
                'disk' => 'public',
                'path' => '',
                'overwrite' => 1,
                'files' => [
                    UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
                ]
            ]));

        Storage::disk('public')->assertMissing('avatar.jpg');

        $response->assertStatus(403);
    }

    public function testFilemanagerWritePublicDiskAsAdmin()
    {
        $user = factory(User::class)->states('admin')->create();

        Auth::login($user, true);

        Storage::fake('public');

        $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post(route('fm.upload'), [
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
        $user = factory(User::class)->states('super-admin')->create();

        Auth::login($user, true);

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
