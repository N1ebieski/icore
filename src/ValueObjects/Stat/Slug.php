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
     * @return array
     */
    public static function getAvailable(): array
    {
        return [self::CLICK, self::VIEW];
    }
}
