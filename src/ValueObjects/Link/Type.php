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

namespace N1ebieski\ICore\ValueObjects\Link;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Type extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const LINK = 'link';

    /**
     * [public description]
     * @var string
     */
    public const BACKLINK = 'backlink';

    /**
     * Undocumented function
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    protected function validate(string $value): void
    {
        $in = [self::LINK, self::BACKLINK];

        if (!in_array($value, $in)) {
            throw new \InvalidArgumentException("The given value must be in: " . implode(', ', $in));
        }
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isLink(): bool
    {
        return $this->value === self::LINK;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isBacklink(): bool
    {
        return $this->value === self::BACKLINK;
    }
}
