<?php

namespace ErrorDumper\Helpers;

/**
 * @deprecated
 * @codeCoverageIgnore
 */
class PHPVersion
{
    public static function atLeast($compareTo)
    {
        return version_compare(PHP_VERSION, $compareTo) >= 0;
    }
}
