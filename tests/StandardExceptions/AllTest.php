<?php

namespace ErrorDumper\Tests\StandardExceptions;

use ErrorDumper\StandardExceptions\FakeException;

class AllTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_testSetters
     * @param FakeException $e
     * @param $code
     * @param $message
     * @param $file
     * @param $line
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

    public function provider_testSetters()
    {
        $result = array();
        $classes = array('FakeException', 'FatalErrorException', 'ShutdownErrorException');
        foreach ($classes as $class)
        {
            $class = 'ErrorDumper\StandardExceptions\\' . $class;
            /** @var FakeException $e */
            $e = new $class;
            for ($i = 0; $i < 10; $i++)
            {
                $result[] = array(
                    $e,
                    mt_rand(0, PHP_INT_MAX),
                    'test ' . uniqid('', true),
                    __FILE__,
                    mt_rand(0, PHP_INT_MAX)
                );
            }
        }

        return $result;
    }
}