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

namespace N1ebieski\ICore\ValueObjects;

use InvalidArgumentException;

class Progress extends ValueObject
{
    /**
     *
     * @param int $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(int $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @param integer $value
     * @return void
     */
    protected function validate(int $value): void
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException("The given value '{$value}' must be between 0 and 100");
        }
    }

    /**
     *
     * @return bool
     */
    public function isAutoTrans(): bool
    {
        return $this->value === 0;
    }

    /**
     *
     * @return bool
     */
    public function isFullTrans(): bool
    {
        return $this->value === 100;
    }
}
