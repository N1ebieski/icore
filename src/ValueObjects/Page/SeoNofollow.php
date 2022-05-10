<?php

namespace N1ebieski\ICore\ValueObjects\Page;

use N1ebieski\ICore\ValueObjects\ValueObject;

class SeoNofollow extends ValueObject
{
    /**
     * [public description]
     * @var int
     */
    public const ACTIVE = 1;

    /**
     * [public description]
     * @var int
     */
    public const INACTIVE = 0;

    /**
     * Undocumented function
     *
     * @param integer $value
     */
    public function __construct(int $value)
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
        $in = [self::ACTIVE, self::INACTIVE];

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
        if (in_array($value, ['active', (string)self::ACTIVE])) {
            return static::active();
        }

        if (in_array($value, ['inactive', (string)self::INACTIVE])) {
            return static::inactive();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function active()
    {
        return new static(self::ACTIVE);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function inactive()
    {
        return new static(self::INACTIVE);
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isInactive(): bool
    {
        return $this->value === self::INACTIVE;
    }
}