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

namespace N1ebieski\ICore\Database\Factories\MailingEmail\User;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\MailingEmail\User\MailingEmail;
use N1ebieski\ICore\Database\Factories\MailingEmail\MailingEmailFactory as BaseMailingEmailFactory;

class MailingEmailFactory extends BaseMailingEmailFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MailingEmail::class;

    /**
    * Undocumented function
    *
    * @return static
    */
    public function withMorph()
    {
        return $this->for(User::makeFactory(), 'morph');
    }
}
