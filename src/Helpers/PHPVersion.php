<?php

namespace ErrorDumper\Helpers;

/**
 * @internal
 * @codeCoverageIgnore
 */
class PHPVersion
{
    public static function atLeast($compareTo)
    {
        return version_compare(PHP_VERSION, $compareTo) >= 0;
    }
}
