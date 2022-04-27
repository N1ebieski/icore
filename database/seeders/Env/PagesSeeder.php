<?php

namespace N1ebieski\ICore\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Page\Page;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();

        $pattern = [
            0 => 6,
            1 => [0, 3],
            2 => [0, 3],
            3 => [0, 3]
        ];

        $depth = 0;

        $closure = function ($parent_id) use ($pattern, $user, &$closure, &$depth) {
            if (is_array($pattern[$depth])) {
                $loop = rand($pattern[$depth][0], $pattern[$depth][1]);
            } else {
                $loop = $pattern[$depth];
            }

            for ($i = 0; $i < $loop; $i++) {
                $page = Page::makeFactory()->for($user)->create([
                    'parent_id' => $parent_id
                ]);

                $depth = $page->real_depth + 1;

                if (isset($pattern[$depth])) {
                    $closure($page->id);
                }
            }
        };

        $closure(null);
    }
}
