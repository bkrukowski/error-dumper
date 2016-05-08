<?php

namespace ErrorDumper\Tests\StackTrace;

use ErrorDumper\StackTrace\Step;
use ErrorDumper\Tests\TestBase;

class StepTest extends TestBase
{
    /**
     * @dataProvider providerTestInFunction
     * @param Step $step
     * @param bool $inFunction
     */
    public function testInFunction(Step $step, $inFunction)
    {
        $this->assertSame($inFunction, $step->inFunction());
    }

    public function providerTestInFunction()
    {
        return array(
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), true),
        );
    }

    /**
     * @dataProvider providerTestInClosure
     * @param Step $step
     * @param bool $inClosure
     */
    public function testInClosure(Step $step, $inClosure)
    {
        $this->assertSame($inClosure, $step->inClosure());
    }

    public function providerTestInClosure()
    {
        return array(
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), false),
            array(new Step(array('function' => Step::CLOSURE_NAME)), true),
        );
    }

    /**
     * @dataProvider providerTestInKeywordFunction
     * @param Step $step
     * @param bool $inKeyword
     */
    public function testInKeywordFunction(Step $step, $inKeyword)
    {
        $this->assertSame($inKeyword, $step->inKeywordFunction());
    }

    public function providerTestInKeywordFunction()
    {
        return array(
            array(new Step(array('function' => 'include')), true),
            array(new Step(array()), false),
            array(new Step(array('function', 'strpos')), false),
        );
    }

    /**
     * @param Step $step
     * @param bool $inClass
     * @dataProvider providerTestInClass
     */
    public function testInClass(Step $step, $inClass)
    {
        $this->assertSame($inClass, $step->inClass());
    }

    public function providerTestInClass()
    {
        return array(
            array(new Step(array('class' => __CLASS__)), true),
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), false),
        );
    }

    /**
     * @dataProvider providerTestInMagicCall
     * @param Step $step
     * @param bool $inCallMethod
     */
    public function testInMagicCall(Step $step, $inCallMethod)
    {
        $this->assertSame($inCallMethod, $step->inMagicCall());
    }

    public function providerTestInMagicCall()
    {
        return array(
            array(new Step(array('class' => __CLASS__, 'function' => 'does_not_exist')), true),
            array(new Step(array('class' => __CLASS__, 'function' => __FUNCTION__)), false),
            array(new Step(array('class' => __CLASS__, 'function' => Step::CLOSURE_NAME)), false),
        );
    }

    public function testGetArguments()
    {
        $noArguments = new Step(array());
        $this->assertEmpty($noArguments->getArguments());
        $this->assertTrue(is_array($noArguments->getArguments()));

        $arguments = new \ArrayObject();
        $withArguments = new Step(array(
            'args' => $arguments,
        ));
        $this->assertSame($arguments, $withArguments->getArguments());
    }

    /**
     * @expectedException \ErrorDumper\StackTrace\NoFunctionException
     */
    public function testGetFunction()
    {
        $method = new Step(array(
            'class' => __CLASS__,
            'function' => __FUNCTION__,
        ));
        $this->assertInstanceOf('ReflectionMethod', $method->getFunction());

        $function = new Step(array(
            'function' => 'strpos',
        ));
        $this->assertInstanceOf('ReflectionFunction', $function->getFunction());

        $noFunction = new Step(array(
            'function' => 'include',
        ));
        $noFunction->getFunction();
    }
}
