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

namespace N1ebieski\ICore\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\BanModel\User\BanModel;

class BansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::take(50)->get()
            ->each(function ($user) {
                App::make(BanModel::class)->morph()->associate($user)->save();
            });
    }
}
