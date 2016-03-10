<?php

namespace ErrorDumper\TestsDumpFunctions;

use ErrorDumper\DumpFunctions\DumpFunctionInterface;
use ErrorDumper\DumpFunctions\InternalVarDumper;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\DumpFunctions\NothingVarDumper;

class AllTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_test___invoke
     * @param DumpFunctionInterface $dumper
     * @param $data
     */
    public function test___invoke(DumpFunctionInterface $dumper, $data)
    {
        $this->assertTrue(is_string($dumper($data)));
    }

    public function provider_test___invoke()
    {
        $data = array(new \stdClass(), $this,  function () {}, range(1, 1000), tmpfile(), null, array());
        $result = array();
        foreach (array(new LightVarDumper(), new InternalVarDumper(), new NothingVarDumper()) as $dumper)
        {
            foreach ($data as $var)
            {
                $result[] = array($dumper, $var);
            }
        }

        return $result;
    }
}