<?php

namespace ErrorDumper\Helpers;

/**
 * @internal
 */
class Exceptions
{
    /**
     * @param \Exception|\Throwable $var
     * @throws NotThrowableException
     */
    public static function throwIfIsNotThrowable($var)
    {
        if (!is_object($var)) {
            throw new NotThrowableException('Variable has to be throwable, but is not even object!');
        }
        if (PHPVersion::atLeast('7.0')) {
            // @codeCoverageIgnoreStart
            if (!$var instanceof \Throwable) {
                throw new NotThrowableException('Variable is not instance of \Throwable!');
            }
            // @codeCoverageIgnoreStop
        } elseif (!$var instanceof \Exception) {
            // @codeCoverageIgnoreStart
            throw new NotThrowableException('Variable is not instance of \Exception!');
            // @codeCoverageIgnoreStop
        }
    }

    /**
     * @param callable $var
     * @throws NotCallableException
     */
    public static function throwIfIsNotCallable($var)
    {
        if (!is_callable($var)) {
            throw new NotCallableException('Variable has to be callable!');
        }
    }
}
