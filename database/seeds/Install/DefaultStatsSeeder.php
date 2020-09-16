<?php

namespace N1ebieski\ICore\Seeds\Install;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Stat\Stat;

class DefaultStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stat::firstOrCreate(['slug' => Stat::VIEW]);
    }
}
