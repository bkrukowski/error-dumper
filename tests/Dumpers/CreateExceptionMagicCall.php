<?php

namespace ErrorDumper\Tests\Dumpers;

/**
 * @method static staticException($foo, $bar)
 * @method exception($foo, $bar)
 */
class CreateExceptionMagicCall
{
    public static function __callStatic($name, $arguments)
    {
        return self::createException();
    }

    public function __call($name, $arguments)
    {
        return self::createException();
    }

    private static function createException()
    {
        return new \Exception();
    }
}
