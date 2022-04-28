<?php

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
