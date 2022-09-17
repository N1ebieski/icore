<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Database\Seeders\Env;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;

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

        Mailing::makeFactory()->count(2)->create()
            ->each(function ($m) use ($users) {

                /**
                 * Dwie metody wrzucania kolekcji do bazy za pomocą 1 inserta. Pierwsza -
                 * tworzymy modele, przerzucamy atrybuty do tablicy i na koniec cała tablicę
                 * insertujemy do modelu
                 */

                $models_model = [];

                foreach ($users as $user) {
                    $email = new MailingEmail();
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

                for ($i = 0; $i < 50; $i++) {
                    $models_email[] = $email = new MailingEmail();
                    $email->email = Faker::create()->unique()->safeEmail;
                }

                $m->emails()->saveMany($models_email);
            });
    }
}
