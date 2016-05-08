<?php

namespace ErrorDumper\Tests\Console;

use ErrorDumper\Console\Printer;
use ErrorDumper\Helpers\Stream;
use ErrorDumper\Tests\TestBase;

class PrinterTest extends TestBase
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
        $this->assertContains($text, Stream::getContentsFromStream($stream));
    }

    public function providerTestMessageBox()
    {
        $data = array(
            'errorBox' => array('errorBox'),
            'infoBox' => array('infoBox'),
            'defaultBox' => array('defaultBox'),
            'warningBox' => array('warningBox'),
        );

        return $this->prepareDataProvider($data);
    }

    public function testSetOutputStream()
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
