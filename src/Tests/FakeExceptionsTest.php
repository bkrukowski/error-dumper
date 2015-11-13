<?php

namespace ErrorDumper\Tests;

use ErrorDumper\FakeException;
use ErrorDumper\FatalErrorException;
use ErrorDumper\ShutdownErrorException;

class FakeExceptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider exceptionDataProvider
     */
    public function testSetters(FakeException $e, $code, $message, $file, $line)
    {
        $this->assertSame($e, $e->setCode($code));
        $this->assertSame($e, $e->setMessage($message));
        $this->assertSame($e, $e->setFile($file));
        $this->assertSame($e, $e->setLine($line));
        $this->assertSame($code, $e->getCode());
        $this->assertSame($message, $e->getMessage());
        $this->assertSame($file, $e->getFile());
        $this->assertSame($line, $e->getLine());
    }

    public function exceptionDataProvider()
    {
        $result = [];
        $classes = [FakeException::class, FatalErrorException::class, ShutdownErrorException::class];
        foreach ($classes as $class)
        {
            /** @var FakeException $e */
            $e = new $class;
            for ($i = 0; $i < 10; $i++)
            {
                $result[] = [
                    $e,
                    mt_rand(0, PHP_INT_MAX),
                    'test ' . uniqid('', true),
                    __FILE__,
                    mt_rand(0, PHP_INT_MAX)
                ];
            }
        }

        return $result;
    }
}