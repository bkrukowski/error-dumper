<?php

namespace ErrorDumper\Tests\Handlers;

use ErrorDumper\Dumpers\DumperInterface;
use ErrorDumper\Handlers\Handler;
use ErrorDumper\Handlers\PreCallbackEvent;
use ErrorDumper\StandardExceptions\FakeException;
use ErrorDumper\Tests\StreamHelper;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testWarmUp()
    {
        $mock = $this->createDumper();
        $stream = tmpfile();
        $this->assertSame($mock, $mock->setOutputStream($stream));
        $message = 'Some error!';
        $fakeException = new FakeException();
        $mock->displayException($fakeException->setMessage($message));
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
        $self = $this;

        $fakeException = new FakeException();
        $e = $fakeException->setMessage($exceptionText);
        $dumper = $this->createDumper();
        $handler = new Handler($dumper);
        /** @var \PHPUnit_Framework_MockObject_MockObject|PreCallbackEvent $preCallback */
        $preCallback = $this->getMockForAbstractClass('ErrorDumper\Handlers\PreCallbackEvent');
        $preCallback->method('__invoke')->willReturnCallback(function ($currentE) use ($stream, $e, $preText, $break, $preCallback, $self) {
            $self->assertSame($e, $currentE);
            fputs($stream, $preText);
            $break && $preCallback->stopDisplay();
        });
        $dumper->setOutputStream($stream);
        $handler->setPreCallback($preCallback);
        $handler->setPostCallback(function ($currentE) use ($stream, $e, $postText, $self) {
            $self->assertSame($e, $currentE);
            fputs($stream, $postText);
        });
        $handler($e);
        $this->assertSame($expected, StreamHelper::getContentsFromStream($stream));
    }

    public function callbacksDataProvider()
    {
        $streamFor3 = tmpfile();
        return array(
            array('pre', ' exception', ' post', false, tmpfile(), 'pre exception post'),
            array('pre', ' exception', ' post', true, tmpfile(), 'pre'),
            array('pre1', ' exception1', ' post1', true, $streamFor3, 'pre1'),
            array(' pre2', ' exception2', ' post2', false, $streamFor3, 'pre1 pre2 exception2 post2'),
            array('pre', ' exception', ' post', false, tmpfile(), 'pre exception post'),
            array(' pre3', ' exception3', ' post3', true, $streamFor3, 'pre1 pre2 exception2 post2 pre3'),
        );
    }

    /**
     * @return DumperInterface
     */
    private function createDumper()
    {
        $mock = $this->getMockBuilder('ErrorDumper\Dumpers\DumperInterface')->getMock();
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