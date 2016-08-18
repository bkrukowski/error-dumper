<?php

namespace ErrorDumper\Tests\Helpers;

use ErrorDumper\Helpers\Stream;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContentsFromStream()
    {
        $stream  = tmpfile();
        fputs($stream, __CLASS__);
        $this->assertSame(__CLASS__, Stream::getContentsFromStream($stream));
        fputs($stream, ' foo');
        $this->assertSame(__CLASS__ . ' foo', Stream::getContentsFromStream($stream));
        fclose($stream);
    }

    public function testGetContents()
    {
        $stream  = tmpfile();
        $streamObj = new Stream($stream);
        fputs($stream, __CLASS__);
        $this->assertSame(__CLASS__, $streamObj->getContents());
        fputs($stream, ' foo');
        $this->assertSame(__CLASS__ . ' foo', $streamObj->getContents());
        fclose($stream);
    }
}
