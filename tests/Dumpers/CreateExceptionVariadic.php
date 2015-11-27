<?php

namespace ErrorDumper\Tests\Dumpers;

class CreateExceptionVariadic
{
    public static function createException()
    {
        return static::_createException('1', 2, 3.1, new \stdClass(), null, []);
    }

    protected static function _createException(...$params)
    {
        $fn = function (...$params) {
            return new \Exception();
        };

        return $fn(...$params);
    }
}