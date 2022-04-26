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
     * @param boolean $value
     * @return void
     */
    protected function validate(bool $value): void
    {
        $in = [self::LINK, self::BACKLINK];

        if (!in_array($value, $in)) {
            throw new \InvalidArgumentException("The given value must be in: " . implode(', ', $in));
        }
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    public static function fromString(string $value)
    {
        if (in_array($value, ['link', self::LINK])) {
            return static::link();
        }

        if (in_array($value, ['backlink', self::BACKLINK])) {
            return static::backlink();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function link()
    {
        return new static(self::LINK);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function backlink()
    {
        return new static(self::BACKLINK);
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
