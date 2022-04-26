<?php

namespace N1ebieski\ICore\ValueObjects\MailingEmail;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Sent extends ValueObject
{
    /**
     * [public description]
     * @var int
     */
    public const SENT = 1;

    /**
     * [public description]
     * @var int
     */
    public const UNSENT = 0;

    /**
     * [public description]
     * @var int
     */
    public const ERROR = 2;

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
     * @param integer $value
     * @return void
     */
    protected function validate(int $value): void
    {
        $in = [self::SENT, self::UNSENT, self::ERROR];

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
        if (in_array($value, ['sent', self::SENT])) {
            return static::sent();
        }

        if (in_array($value, ['unsent', self::UNSENT])) {
            return static::unsent();
        }

        if (in_array($value, ['error', self::ERROR])) {
            return static::error();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function sent()
    {
        return new static(self::SENT);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function unsent()
    {
        return new static(self::UNSENT);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function error()
    {
        return new static(self::ERROR);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isSent(): bool
    {
        return $this->value === self::SENT;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUnsent(): bool
    {
        return $this->value === self::UNSENT;
    }
}
