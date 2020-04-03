<?php

namespace N1ebieski\ICore\Seeds\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Models\Category\Post\Category;

/**
 * [LinksSeeder description]
 */
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

        factory(Link::class, 10)->states('link')->create()
            ->each(function ($link) use ($categories) {
                shuffle($categories);
                $link->categories()->attach(array_slice($categories, 0, rand(1, 5)));
            });
    }
}
