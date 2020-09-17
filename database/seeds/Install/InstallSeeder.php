<?php

namespace N1ebieski\ICore\Seeds\Install;

use Illuminate\Database\Seeder;

class InstallSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultRolesAndPermissionsSeeder::class);
        $this->call(DefaultStatsSeeder::class);
    }
}
