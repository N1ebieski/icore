<?php

namespace N1ebieski\ICore\Models\MailingEmail\User;

use N1ebieski\ICore\Database\Factories\MailingEmail\User\MailingEmailFactory;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail as BaseMailingEmail;

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
