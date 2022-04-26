<?php

namespace N1ebieski\ICore\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Stat\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;

class DefaultStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stat::firstOrCreate(['slug' => Slug::VIEW]);
    }
}
