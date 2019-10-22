<?php

namespace N1ebieski\ICore\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\User;

/**
 * [PagesSeeder description]
 */
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

        for ($i=0; $i<6; $i++) {
            $page1[$i] = factory(Page::class)->make();
            $page1[$i]->user()->associate($user);
            $page1[$i]->save();

            for ($j=0; $j<rand(0, 3); $j++) {
                $page2[$j] = factory(Page::class)->make();
                $page2[$j]->user()->associate($user);
                $page2[$j]->parent_id = $page1[$i]->id;
                $page2[$j]->save();

                for ($k=0; $k<rand(0, 3); $k++) {
                    $page3[$k] = factory(Page::class)->make();
                    $page3[$k]->user()->associate($user);
                    $page3[$k]->parent_id = $page2[$j]->id;
                    $page3[$k]->save();

                    for ($l=0; $l<rand(0, 3); $l++) {
                        $page4[$l] = factory(Page::class)->make();
                        $page4[$l]->user()->associate($user);
                        $page4[$l]->parent_id = $page3[$k]->id;
                        $page4[$l]->save();
                    }
                }
            }
        }
    }
}
