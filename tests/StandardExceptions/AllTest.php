<?php

namespace ErrorDumper\Tests\StandardExceptions;

use ErrorDumper\StandardExceptions\FakeException;
use ErrorDumper\Tests\TestBase;

class AllTest extends TestBase
{
    /**
     * @dataProvider providerTestSetters
     * @param FakeException $exception
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     */
    public function testSetters(FakeException $exception, $code, $message, $file, $line)
    {
        $this->assertSame($exception, $exception->setCode($code));
        $this->assertSame($exception, $exception->setMessage($message));
        $this->assertSame($exception, $exception->setFile($file));
        $this->assertSame($exception, $exception->setLine($line));
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($file, $exception->getFile());
        $this->assertSame($line, $exception->getLine());
    }

    public function providerTestSetters()
    {
        $result = array();
        $classes = array('FakeException', 'FatalErrorException', 'ShutdownErrorException');
        foreach ($classes as $class) {
            $class = 'ErrorDumper\StandardExceptions\\' . $class;
            /** @var FakeException $e */
            $e = new $class;
            for ($i = 0; $i < 10; $i++) {
                $result[] = array(
                    $e,
                    mt_rand(0, PHP_INT_MAX),
                    'test ' . uniqid('', true),
                    __FILE__,
                    mt_rand(0, PHP_INT_MAX)
                );
            }
        }

        return $this->prepareDataProvider($result);
    }
}
