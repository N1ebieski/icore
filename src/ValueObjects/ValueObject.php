<?php

namespace N1ebieski\ICore\ValueObjects;

abstract class ValueObject
{
    /**
     * Undocumented variable
     *
     * @var mixed
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Undocumented function
     *
     * @param static $value
     * @return boolean
     */
    public function isEquals(self $value): bool
    {
        return $this->value === $value->value;
    }
}
