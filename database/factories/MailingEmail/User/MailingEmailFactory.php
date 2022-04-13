<?php

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
