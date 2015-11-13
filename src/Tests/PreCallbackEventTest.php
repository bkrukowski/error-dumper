<?php

namespace ErrorDumper\Tests;

use ErrorDumper\PreCallbackEvent;

class PreCallbackEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \ErrorDumper\StopDisplayException
     */
    public function testStopDisplay()
    {
        $callback = $this->createPreCallbackMock();
        $callback->stopDisplay();
    }

    /**
     * @return PreCallbackEvent
     */
    private function createPreCallbackMock()
    {
        return $this->getMockForAbstractClass(PreCallbackEvent::class);
    }
}