<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\Cli;
use ErrorDumper\Helpers\Stream;

class CliTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $stream = tmpfile();
        $cli = new Cli($stream);
        $called = false;
        $returnedCli = $cli->setWindowWidthGetter(function () use (&$called) {
            $called = true;

            return 35;
        });
        $this->assertSame($cli, $returnedCli);
        $cli->displayException(new \Exception(__FILE__));
        $output = Stream::getContentsFromStream($stream);
        $this->assertTrue($called);
        $this->assertContains(__FILE__, $output);
        $this->assertContains(str_pad('', 35, '#'), $output);
    }
}