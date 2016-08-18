<?php

namespace ErrorDumper\Tests\DumpFunctions;

use ErrorDumper\DumpFunctions\DumpFunctionInterface;
use ErrorDumper\DumpFunctions\InternalVarDumper;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\DumpFunctions\NothingVarDumper;

class AllTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestInvoke
     * @param DumpFunctionInterface $dumper
     * @param $data
     */
    public function testInvoke(DumpFunctionInterface $dumper, $data)
    {
        $this->assertTrue(is_string($dumper($data)));
    }

    public function providerTestInvoke()
    {
        $function = function () {
        };
        $data = array(new \stdClass(), $this, $function, range(1, 1000), tmpfile(), null, array(), new DebugInfo());
        $result = array();
        foreach (array(new LightVarDumper(), new InternalVarDumper(), new NothingVarDumper()) as $dumper) {
            foreach ($data as $var) {
                $result[] = array($dumper, $var);
            }
        }

        return $result;
    }
}
