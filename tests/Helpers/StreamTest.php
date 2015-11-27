<?php

namespace ErrorDumper\Tests\Helpers;

use ErrorDumper\Helpers\Stream;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function test_getContentsFromStream()
    {
        $stream  = tmpfile();
        fputs($stream, __CLASS__);
        $this->assertSame(__CLASS__, Stream::getContentsFromStream($stream));
        fputs($stream, ' foo');
        $this->assertSame(__CLASS__ . ' foo', Stream::getContentsFromStream($stream));
        fclose($stream);
    }
}