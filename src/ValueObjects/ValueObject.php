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

use Stringable;

abstract class ValueObject implements Stringable
{
    /**
     * Undocumented variable
     *
     * @var mixed
     */
    protected $value;

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Get undocumented variable
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Undocumented function
     *
     * @param static $value
     * @return boolean
     */
    public function isEquals(self $value): bool
    {
        return $this->value === $value->value;
    }
}
