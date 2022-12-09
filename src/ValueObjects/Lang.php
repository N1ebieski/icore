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
use N1ebieski\ICore\ValueObjects\ValueObject;

class Lang extends ValueObject
{
    /**
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    /**
     *
     * @param string $value
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validate(string $value): void
    {
        if (strlen($value) !== 2) {
            throw new \InvalidArgumentException("The given value: '{$value}' must have 2 characters in alpha-2 country code");
        }
    }
}
