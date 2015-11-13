<?php

namespace ErrorDumper\DumpFunctions\Tests;

use ErrorDumper\DumpFunctions\InternalVarDumper;

class InternalVarDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider varDumpProvider
     * @param $var
     */
    public function testVarDump($var)
    {
        $varDumper = new InternalVarDumper();
        $result = $varDumper($var);
        $this->assertTrue(is_string($result), $result);
        $this->assertTrue(strlen($result) > 0, $result);
    }

    public function varDumpProvider()
    {
        return [
            ['foo'],
            [123],
            [0],
            [false],
            [true],
            [new \stdClass()],
            [[]],
            [null],
        ];
    }
}