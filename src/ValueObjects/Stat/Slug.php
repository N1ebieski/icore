<?php

namespace N1ebieski\ICore\ValueObjects\Stat;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Slug extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const CLICK = 'click';

    /**
     * [public description]
     * @var string
     */
    public const VIEW = 'view';

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
        $in = [self::CLICK, self::VIEW];

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
        if (in_array($value, ['click', self::CLICK])) {
            return static::click();
        }

        if (in_array($value, ['view', self::VIEW])) {
            return static::view();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function click()
    {
        return new static(self::CLICK);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function view()
    {
        return new static(self::VIEW);
    }
}
