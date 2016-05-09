<?php

namespace ErrorDumper\Tests\Dumpers;

class CreateExceptionVariadic
{
    public static function createException()
    {
        return self::finalCreateException('1', 2, 3.1, new \stdClass(), null, array());
    }

    public static function createException2()
    {
        return self::finalCreateException(1, 2);
    }

    private static function finalCreateException(...$params)
    {
        $fn = function (...$params) {
            return new \Exception();
        };

        return $fn(...$params);
    }
}
