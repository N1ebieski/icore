<?php

namespace N1ebieski\ICore\ValueObjects;

abstract class ValueObject
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $value;

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Get undocumented variable
     *
     * @return  int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Undocumented function
     *
     * @param self $value
     * @return boolean
     */
    public function isEquals(self $value): bool
    {
        return $this->value === $value->value;
    }
}
