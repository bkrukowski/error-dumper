<?php

namespace ErrorDumper\Tests\Helpers;

use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\Tests\TestBase;

class ExceptionsTest extends TestBase
{
    /**
     * @dataProvider provider_test_throwIfIsNotThrowable
     * @param $value
     * @param bool $throwable
     */
    public function test_throwIfIsNotThrowable($value, $throwable)
    {
        if (!$throwable)
        {
            $this->setExpectedException('ErrorDumper\Helpers\NotThrowableException');
        }
        Exceptions::throwIfIsNotThrowable($value);
    }

    public function provider_test_throwIfIsNotThrowable()
    {
        return $this->prepareDataProvider(array(
            array(1, false),
            array(new \stdClass(), false),
            array(array(), false),
            array(new \Exception(), true),
        ));
    }

    /**
     * @dataProvider provider_test_throwIfIsNotCallable
     * @param $value
     * @param bool $callable
     */
    public function test_throwIfIsNotCallable($value, $callable)
    {
        if (!$callable)
        {
            $this->setExpectedException('ErrorDumper\Helpers\NotCallableException');
        }
        Exceptions::throwIfIsNotCallable($value);
    }

    public function provider_test_throwIfIsNotCallable()
    {
        return $this->prepareDataProvider(array(
            array(1, false),
            array(new \stdClass(), false),
            array('strpos', true),
            array(function () {}, true),
            array(array(new static(), 'testFunction'), true),
            array(__CLASS__ . '::testFunction', true)
        ));
    }

    public static function testFunction()
    {
    }
}