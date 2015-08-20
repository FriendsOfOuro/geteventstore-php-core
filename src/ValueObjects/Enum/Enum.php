<?php

namespace ValueObjects\Enum;

use MabeEnum\Enum as BaseEnum;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

abstract class Enum extends BaseEnum implements ValueObjectInterface
{
    /**
     * Returns a new Enum object from passed value matching argument
     *
     * @param  string $value
     * @return static
     */
    public static function fromNative()
    {
        return static::get(func_get_arg(0));
    }

    /**
     * Returns the PHP native value of the enum
     *
     * @return mixed
     */
    public function toNative()
    {
        return parent::getValue();
    }

    /**
     * Tells whether two Enum objects are sameValueAs by comparing their values
     *
     * @param  Enum $enum
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $enum)
    {
        if (false === Util::classEquals($this, $enum)) {
            return false;
        }

        return $this->toNative() === $enum->toNative();
    }

    /**
     * Returns a native string representation of the Enum value
     *
     * @return string
     */
    public function __toString()
    {
        return \strval($this->toNative());
    }
}
