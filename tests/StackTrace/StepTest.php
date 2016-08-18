<?php

namespace ErrorDumper\Tests\StackTrace;

use ErrorDumper\StackTrace\Step;

class StepTest extends StepProviders
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

    /**
     * @dataProvider providerTestInClosure
     * @param Step $step
     * @param bool $inClosure
     */
    public function testInClosure(Step $step, $inClosure)
    {
        $this->assertSame($inClosure, $step->inClosure());
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

    /**
     * @param Step $step
     * @param bool $inClass
     * @dataProvider providerTestInClass
     */
    public function testInClass(Step $step, $inClass)
    {
        $this->assertSame($inClass, $step->inClass());
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
