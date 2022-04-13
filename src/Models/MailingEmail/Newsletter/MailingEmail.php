<?php

namespace N1ebieski\ICore\Models\MailingEmail\Newsletter;

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

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\ICore\Models\Newsletter::class;
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'newsletter';
    }
}
