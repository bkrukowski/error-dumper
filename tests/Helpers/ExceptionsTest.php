<?php

namespace ErrorDumper\Tests\Helpers;

use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\Tests\TestBase;

class ExceptionsTest extends TestBase
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
        Exceptions::throwIfIsNotThrowable($value);
    }

    public function providerTestThrowIfIsNotThrowable()
    {
        return $this->prepareDataProvider(array(
            array(1, false),
            array(new \stdClass(), false),
            array(array(), false),
            array(new \Exception(), true),
        ));
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
        Exceptions::throwIfIsNotCallable($value);
    }

    public function providerTestThrowIfIsNotCallable()
    {
        $emptyFn = function () {
        };
        return $this->prepareDataProvider(array(
            array(1, false),
            array(new \stdClass(), false),
            array('strpos', true),
            array($emptyFn, true),
            array(array(new static(), 'testFunction'), true),
            array(__CLASS__ . '::testFunction', true)
        ));
    }

    public static function testFunction()
    {
    }
}
