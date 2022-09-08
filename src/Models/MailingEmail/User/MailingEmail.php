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

namespace N1ebieski\ICore\Models\MailingEmail\User;

use N1ebieski\ICore\Models\MailingEmail\MailingEmail as BaseMailingEmail;
use N1ebieski\ICore\Database\Factories\MailingEmail\User\MailingEmailFactory;

class MailingEmail extends BaseMailingEmail
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\MailingEmail\MailingEmail::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return MailingEmailFactory
     */
    protected static function newFactory()
    {
        return MailingEmailFactory::new();
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\ICore\Models\User::class;
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'user';
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return MailingEmailFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
