<?php

namespace ErrorDumper\Tests\Console;

use ErrorDumper\Console\Printer;
use ErrorDumper\Helpers\Stream;

class PrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestMessageBox
     * @param $functionName
     */
    public function testMessageBox($functionName)
    {
        $stream = tmpfile();
        $text = __FILE__;
        $printer = new Printer($stream);
        $this->assertSame($printer, $printer->$functionName($text));
        $streamObj = new Stream($stream);
        $this->assertContains($text, $streamObj->getContents());
    }

    public function providerTestMessageBox()
    {
        return array(
            'errorBox' => array('errorBox'),
            'infoBox' => array('infoBox'),
            'defaultBox' => array('defaultBox'),
            'warningBox' => array('warningBox'),
        );
    }

    public function testSetOutputStream()
    {
        $firstStream = tmpfile();
        $secondStream = tmpfile();
        $text = __FILE__;
        $printer = new Printer($firstStream);
        $this->assertSame($printer, $printer->setOutputStream($secondStream));
        $printer->defaultBox($text);
        $firstStreamObj = new Stream($firstStream);
        $secondStreamObj = new Stream($secondStream);
        $this->assertSame('', $firstStreamObj->getContents());
        $this->assertContains($text, $secondStreamObj->getContents());
    }
}
