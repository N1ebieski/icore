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

namespace N1ebieski\ICore\Database\Factories\Mailing;

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use N1ebieski\ICore\Models\MailingLang\MailingLang;

class MailingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Mailing>
     */
    protected $model = Mailing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'status' => Status::INACTIVE
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return static
     */
    public function configure()
    {
        return $this->afterCreating(function (Mailing $mailing) {
            foreach (Config::get('icore.multi_langs') as $lang) {
                MailingLang::makeFactory()->for($mailing)->create([
                    'lang' => $lang
                ]);
            }
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function active()
    {
        return $this->state(function () {
            return [
                'status' => Status::ACTIVE
            ];
        });
    }

    /**
     *
     * @return static
     */
    public function withoutLangs()
    {
        return $this->afterCreating(function (Mailing $mailing) {
            $mailing->langs()->delete();
        });
    }
}
