<?php

namespace ErrorDumper\Tests\StackTrace;

use ErrorDumper\Tests\TestBase;
use ErrorDumper\StackTrace\Step;

class StepProviders extends TestBase
{
    public function providerTestInFunction()
    {
        return array(
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), true),
        );
    }

    public function providerTestInClosure()
    {
        return array(
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), false),
            array(new Step(array('function' => Step::CLOSURE_NAME)), true),
        );
    }

    public function providerTestInKeywordFunction()
    {
        return array(
            array(new Step(array('function' => 'include')), true),
            array(new Step(array()), false),
            array(new Step(array('function', 'strpos')), false),
        );
    }

    public function providerTestInClass()
    {
        return array(
            array(new Step(array('class' => __CLASS__)), true),
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), false),
        );
    }

    public function providerTestInMagicCall()
    {
        return array(
            array(new Step(array('class' => __CLASS__, 'function' => 'does_not_exist')), true),
            array(new Step(array('class' => __CLASS__, 'function' => __FUNCTION__)), false),
            array(new Step(array('class' => __CLASS__, 'function' => Step::CLOSURE_NAME)), false),
        );
    }
}