<?php

namespace ErrorDumper\Tests\Console;

use ErrorDumper\Console\Printer;
use ErrorDumper\Helpers\Stream;

class PrinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_testMessageBox
     * @param $functionName
     */
    public function testMessageBox($functionName)
    {
        $stream = tmpfile();
        $text = __FILE__;
        $printer = new Printer($stream);
        $this->assertSame($printer, $printer->$functionName($text));
        $this->assertContains($text, Stream::getContentsFromStream($stream));
    }

    public function provider_testMessageBox()
    {
        return array(
            array('errorBox'),
            array('infoBox'),
            array('defaultBox'),
            array('warningBox'),
        );
    }

    public function test_setOutputStream()
    {
        $firstStream = tmpfile();
        $secondStream = tmpfile();
        $text = __FILE__;
        $printer = new Printer($firstStream);
        $this->assertSame($printer, $printer->setOutputStream($secondStream));
        $printer->defaultBox($text);
        $this->assertSame('', Stream::getContentsFromStream($firstStream));
        $this->assertContains($text, Stream::getContentsFromStream($secondStream));
    }
}