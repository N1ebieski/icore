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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileManagerTest extends TestCase
{
    use DatabaseTransactions;

    public function testFilemanagerReadPublicDiskAsGuest(): void
    {
        $response = $this->getJson(route('fm.content', ['disk' => 'public']));

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testFilemanagerWritePublicDiskAsGuest(): void
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

    public function testFilemanagerReadPublicDiskAsUser(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->getJson(route('fm.content', ['disk' => 'public']));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testFilemanagerWritePublicDiskAsUser(): void
    {
        /** @var User */
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

    public function testFilemanagerWritePublicDiskAsAdmin(): void
    {
        /** @var User */
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

    public function testFilemanagerWritePublicVendorDiskAsSuperadmin(): void
    {
        /** @var User */
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
