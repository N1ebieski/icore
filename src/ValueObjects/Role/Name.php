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
    * @return array
    */
    public static function getDefault(): array
    {
        return [self::SUPER_ADMIN, self::ADMIN, self::USER, self::API];
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
     * Undocumented function
     *
     * @return boolean
     */
    public function isApi(): bool
    {
        return $this->value === self::API;
    }

    /**
     * [isEditDefault description]
     * @return bool [description]
     */
    public function isDefault(): bool
    {
        return in_array($this->value, self::getDefault());
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
        return !in_array($this->value, self::getDefault());
    }
}
