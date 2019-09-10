<?php

namespace Seeds\ICore;

use Illuminate\Database\Seeder;

/**
 * [DatabaseSeeder description]
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(PostsSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->call(PagesSeeder::class);
        //$this->call(MailingsSeeder::class);
    }
}
