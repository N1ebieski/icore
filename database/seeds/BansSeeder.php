<?php

namespace N1ebieski\ICore\Seeds;

use Illuminate\Database\Seeder;
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
        $users = User::take(50)->get();

        foreach($users as $user) {
            app(BanModel::class)->morph()->associate($user)->save();
        }
    }
}
