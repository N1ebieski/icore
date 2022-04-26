<?php

namespace N1ebieski\ICore\ValueObjects\BanValue;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Type extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const IP = 'ip';

    /**
     * [public description]
     * @var string
     */
    public const WORD = 'word';

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
        $in = [self::IP, self::WORD];

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
        if (in_array($value, ['ip', self::IP])) {
            return static::ip();
        }

        if (in_array($value, ['word', self::WORD])) {
            return static::word();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function ip()
    {
        return new static(self::IP);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function word()
    {
        return new static(self::WORD);
    }
}
