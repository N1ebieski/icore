<?php

namespace N1ebieski\ICore\Seeds\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Tag\Tag;
use Faker\Factory as Faker;

/**
 * [TagsSeeder description]
 */
class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 20000; $i++) {
            $tag = Tag::make();
            $tag->name = Faker::create()->sentence(rand(1, 3));
            $tags_model[] = $tag->attributesToArray();
        }

        Tag::insert($tags_model);
    }
}
