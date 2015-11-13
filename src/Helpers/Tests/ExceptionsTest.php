<?php

namespace ErrorDumper\Helpers\Tests;

use ErrorDumper\Helpers\Exceptions;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \ErrorDumper\Helpers\NotThrowableException
     * @dataProvider nonThrowableProvider
     */
    public function testNonThrowable($var)
    {
        Exceptions::throwIfIsNotThrowable($var);
    }

    public function testWithoutAssertions()
    {
        Exceptions::throwIfIsNotThrowable(new \Exception());
    }

    public function nonThrowableProvider()
    {
        return [
            [123],
            [123.5],
            ['foo'],
            [new \stdClass()],
        ];
    }
}