<?php

namespace N1ebieski\ICore\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Models\Category\Post\Category;

class LinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::active()->get(['id'])->pluck('id')->toArray();

        Link::makeFactory()->count(10)->link()->create()
            ->each(function ($link) use ($categories) {
                shuffle($categories);
                $link->categories()->attach(array_slice($categories, 0, rand(1, 5)));
            });
    }
}
