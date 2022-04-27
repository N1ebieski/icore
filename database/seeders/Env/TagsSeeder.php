<?php

namespace N1ebieski\ICore\Database\Seeders\Env;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Tag\Tag;

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
            $tag = Tag::makeFactory()->make([
                'name' => Faker::create()->sentence(rand(1, 3))
            ]);

            $tags_model[] = $tag->attributesToArray();
        }

        Tag::insert($tags_model);
    }
}
