<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\Cli;
use ErrorDumper\Tests\StreamHelper;

class CliTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $stream = tmpfile();
        $cli = new Cli($stream);
        //$cli->displayException(new \Exception(__FILE__));
        //$this->assertContains(__FILE__, StreamHelper::getContentsFromStream($stream));
    }
}