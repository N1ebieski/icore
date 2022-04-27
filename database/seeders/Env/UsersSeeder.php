<?php

namespace N1ebieski\ICore\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::makeFactory()->count(5000)->user()->marketing()->active()->create();
    }
}
