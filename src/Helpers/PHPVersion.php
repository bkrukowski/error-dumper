<?php

namespace ErrorDumper\Helpers;

/**
 * @codeCoverageIgnore
 */
class PHPVersion
{
    public static function atLeast($compareTo)
    {
        return version_compare(PHP_VERSION, $compareTo) >= 0;
    }
}