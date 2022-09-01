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
     * @return self
     */
    public static function fromString(string $value): self
    {
        if (in_array($value, ['sent', (string)self::SENT])) {
            return self::sent();
        }

        if (in_array($value, ['unsent', (string)self::UNSENT])) {
            return self::unsent();
        }

        if (in_array($value, ['error', (string)self::ERROR])) {
            return self::error();
        }

        throw new \InvalidArgumentException("Invalid string value: '{$value}'");
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function sent(): self
    {
        return new self(self::SENT);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function unsent(): self
    {
        return new self(self::UNSENT);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public static function error(): self
    {
        return new self(self::ERROR);
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
