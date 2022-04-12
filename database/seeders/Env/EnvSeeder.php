<?php

namespace N1ebieski\ICore\Database\Seeders\Env;

use Illuminate\Database\Seeder;

/**
 * [DatabaseSeeder description]
 */
class EnvSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(\N1ebieski\ICore\Database\Seeders\Install\InstallSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(PostsSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->call(PagesSeeder::class);
        $this->call(LinksSeeder::class);
        //$this->call(MailingsSeeder::class);
    }
}
