<?php

namespace N1ebieski\ICore\ValueObjects\Mailing;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Status extends ValueObject
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
     * [public description]
     * @var int
     */
    public const SCHEDULED = 2;

    /**
     * [public description]
     * @var int
     */
    public const INPROGRESS = 3;

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
        $in = [self::ACTIVE, self::INACTIVE, self::SCHEDULED, self::INPROGRESS];

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
        if (in_array($value, ['active', self::ACTIVE])) {
            return static::active();
        }

        if (in_array($value, ['inactive', self::INACTIVE])) {
            return static::inactive();
        }

        if (in_array($value, ['scheduled', self::SCHEDULED])) {
            return static::scheduled();
        }

        if (in_array($value, ['inprogress', self::INPROGRESS])) {
            return static::inprogress();
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
     * Undocumented function
     *
     * @return static
     */
    public static function scheduled()
    {
        return new static(self::SCHEDULED);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public static function inprogress()
    {
        return new static(self::INPROGRESS);
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

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isScheduled(): bool
    {
        return $this->value === self::SCHEDULED;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isInprogress(): bool
    {
        return $this->value === self::INPROGRESS;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isRunning(): bool
    {
        return in_array($this->value, [self::ACTIVE, self::INPROGRESS]);
    }
}
