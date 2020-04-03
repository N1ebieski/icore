<?php

namespace N1ebieski\ICore\Seeds\Env;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\BanModel\User\BanModel;

/**
 * [BansSeeder description]
 */
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
