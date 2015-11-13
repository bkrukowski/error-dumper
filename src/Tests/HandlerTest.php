<?php

namespace ErrorDumper\Tests;

use ErrorDumper\DumperInterface;
use ErrorDumper\FakeException;
use ErrorDumper\Handler;
use ErrorDumper\PreCallbackEvent;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testWarmUp()
    {
        $mock = $this->createDumper();
        $stream = tmpfile();
        $this->assertSame($mock, $mock->setOutputStream($stream));
        $message = 'Some error!';
        $mock->displayException((new FakeException())->setMessage($message));
        $this->assertSame($message, StreamHelper::getContentsFromStream($stream));
        fclose($stream);
    }

    public function testGetSet()
    {
        $mock = $this->createDumper();
        $mock2 = $this->createDumper();
        $this->assertNotSame($mock, $mock2);
        $handler = new Handler($mock);
        $this->assertSame($mock, $handler->getDumper());
        $this->assertSame(
            $handler,
            $handler->setDumper($mock2)
        );
        $this->assertSame($mock2, $handler->getDumper());
        $this->assertSame($handler, $handler->setPostCallback(function () {}));
        $this->assertSame($handler, $handler->setPreCallback(function () {}));
    }

    /**
     * @dataProvider callbacksDataProvider
     */
    public function testCallbacks($preText, $exceptionText, $postText, $break, $stream, $expected)
    {
        $e = (new FakeException())->setMessage($exceptionText);
        $dumper = $this->createDumper();
        $handler = new Handler($dumper);
        /** @var \PHPUnit_Framework_MockObject_MockObject|PreCallbackEvent $preCallback */
        $preCallback = $this->getMockForAbstractClass(PreCallbackEvent::class);
        $preCallback->method('__invoke')->willReturnCallback(function ($currentE) use ($stream, $e, $preText, $break, $preCallback) {
            $this->assertSame($e, $currentE);
            fputs($stream, $preText);
            $break && $preCallback->stopDisplay();
        });
        $dumper->setOutputStream($stream);
        $handler->setPreCallback($preCallback);
        $handler->setPostCallback(function ($currentE) use ($stream, $e, $postText) {
            $this->assertSame($e, $currentE);
            fputs($stream, $postText);
        });
        $handler($e);
        $this->assertSame($expected, StreamHelper::getContentsFromStream($stream));
    }

    public function callbacksDataProvider()
    {
        $streamFor3 = tmpfile();
        return [
            ['pre', ' exception', ' post', false, tmpfile(), 'pre exception post'],
            ['pre', ' exception', ' post', true, tmpfile(), 'pre'],
            ['pre1', ' exception1', ' post1', true, $streamFor3, 'pre1'],
            [' pre2', ' exception2', ' post2', false, $streamFor3, 'pre1 pre2 exception2 post2'],
            ['pre', ' exception', ' post', false, tmpfile(), 'pre exception post'],
            [' pre3', ' exception3', ' post3', true, $streamFor3, 'pre1 pre2 exception2 post2 pre3'],
        ];
    }

    /**
     * @return DumperInterface
     */
    private function createDumper()
    {
        $mock = $this->getMockBuilder(DumperInterface::class)->getMock();
        $stream = fopen('php://output', 'w');
        $mock->method('displayException')->willReturnCallback(function (\Exception $e) use (&$stream) {
            fputs($stream, $e->getMessage());
        });
        $mock->method('setOutputStream')->willReturnCallback(function ($newStream) use (&$stream, $mock) {
            $stream = $newStream;

            return $mock;
        });

        return $mock;
    }
}