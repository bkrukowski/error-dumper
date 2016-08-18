<?php

namespace ErrorDumper\Tests\Helpers;

use ErrorDumper\Helpers\Exceptions;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestThrowIfIsNotThrowable
     * @param $value
     * @param bool $throwable
     */
    public function testThrowIfIsNotThrowable($value, $throwable)
    {
        if (!$throwable) {
            $this->setExpectedException('ErrorDumper\Helpers\NotThrowableException');
        }
        $exceptions = new Exceptions();
        $exceptions->throwIfIsNotThrowable($value);
    }

    public function providerTestThrowIfIsNotThrowable()
    {
        return array(
            array(1, false),
            array(new \stdClass(), false),
            array(array(), false),
            array(new \Exception(), true),
        );
    }

    /**
     * @dataProvider providerTestThrowIfIsNotCallable
     * @param $value
     * @param bool $callable
     */
    public function testThrowIfIsNotCallable($value, $callable)
    {
        if (!$callable) {
            $this->setExpectedException('ErrorDumper\Helpers\NotCallableException');
        }
        $exceptions = new Exceptions();
        $exceptions->throwIfIsNotCallable($value);
    }

    public function providerTestThrowIfIsNotCallable()
    {
        $emptyFn = function () {
        };
        return array(
            array(1, false),
            array(new \stdClass(), false),
            array('strpos', true),
            array($emptyFn, true),
            array(array(new static(), 'testFunction'), true),
            array(__CLASS__ . '::testFunction', true)
        );
    }

    public static function testFunction()
    {
    }
}
