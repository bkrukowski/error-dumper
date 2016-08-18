<?php

namespace ErrorDumper\Tests;

/**
 * @deprecated
 */
abstract class TestBase extends \PHPUnit_Framework_TestCase
{
    protected function prepareDataProvider(array $data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $result[$key . ' ' . phpversion()] = $value;
        }

        return $result;
    }
}
