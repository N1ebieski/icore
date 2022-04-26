<?php

namespace N1ebieski\ICore\ValueObjects\Role;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Name extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const USER = 'user';

    /**
     * [public description]
     * @var string
     */
    public const ADMIN = 'admin';

    /**
     * [public description]
     * @var string
     */
    public const SUPER_ADMIN = 'super-admin';

    /**
     * [public description]
     * @var string
     */
    public const API = 'api';

    /**
     * Undocumented function
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUser(): bool
    {
        return $this->value === self::USER;
    }

    /**
     * [isEditDefault description]
     * @return bool [description]
     */
    public function isEditNotDefault(): bool
    {
        return !in_array($this->value, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * [isDeleteDefault description]
     * @return bool [description]
     */
    public function isDeleteNotDefault(): bool
    {
        return !in_array($this->value, [self::SUPER_ADMIN, self::ADMIN, self::USER, self::API]);
    }
}
