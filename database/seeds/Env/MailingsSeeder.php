<?php

namespace N1ebieski\ICore\Seeds\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\Models\MailingEmail;
use Faker\Factory as Faker;

/**
 * [MailingsSeeder description]
 */
class MailingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        factory(Mailing::class, 2)->create()
            ->each(function ($m) use ($users) {

                /**
                 * Dwie metody wrzucania kolekcji do bazy za pomocą 1 inserta. Pierwsza -
                 * tworzymy modele, przerzucamy atrybuty do tablicy i na koniec cała tablicę
                 * insertujemy do modelu
                 */

                foreach ($users as $user) {
                    $email = MailingEmail::make();
                    $email->email = $user->email;
                    $email->morph()->associate($user);
                    $email->mailing()->associate($m);
                    $models_model[] = $email->attributesToArray();
                }

                MailingEmail::insert($models_model);

                /**
                 * Druga metoda. Tworzymy tablicę modeli i na koniec wrzucamy ją do
                 * bazy za pomocą metody saveMany. Niestety z niewiadomych dla mnie
                 * przyczyn saveMany dostępne jest wyłącznie przez odwołanie przez relację
                 * belongsToMany i HasOneOrMany. createMany tak samo.
                 */

                for ($i=0; $i<50; $i++) {
                    $models_email[] = $email = MailingEmail::make();
                    //$email->mailing()->associate($m);
                    $email->email = Faker::create()->unique()->safeEmail;
                }

                $m->emails()->saveMany($models_email);
            });
    }
}
