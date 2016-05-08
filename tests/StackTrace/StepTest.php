<?php

namespace ErrorDumper\Tests\StackTrace;

use ErrorDumper\StackTrace\Step;
use ErrorDumper\Tests\TestBase;

class StepTest extends TestBase
{
    /**
     * @dataProvider provider_test_inFunction
     * @param Step $step
     * @param bool $inFunction
     */
    public function test_inFunction(Step $step, $inFunction)
    {
        $this->assertSame($inFunction, $step->inFunction());
    }

    public function provider_test_inFunction()
    {
        return array(
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), true),
        );
    }

    /**
     * @dataProvider provider_test_inClosure
     * @param Step $step
     * @param bool $inClosure
     */
    public function test_inClosure(Step $step, $inClosure)
    {
        $this->assertSame($inClosure, $step->inClosure());
    }

    public function provider_test_inClosure()
    {
        return array(
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), false),
            array(new Step(array('function' => Step::CLOSURE_NAME)), true),
        );
    }

    /**
     * @dataProvider provider_test_inKeywordFunction
     * @param Step $step
     * @param bool $inKeyword
     */
    public function test_inKeywordFunction(Step $step, $inKeyword)
    {
        $this->assertSame($inKeyword, $step->inKeywordFunction());
    }

    public function provider_test_inKeywordFunction()
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
     * @dataProvider provider_test_inClass
     */
    public function test_inClass(Step $step, $inClass)
    {
        $this->assertSame($inClass, $step->inClass());
    }

    public function provider_test_inClass()
    {
        return array(
            array(new Step(array('class' => __CLASS__)), true),
            array(new Step(array()), false),
            array(new Step(array('function' => 'strpos')), false),
        );
    }

    /**
     * @dataProvider provider_test_inMagicCall
     * @param Step $step
     * @param bool $inCallMethod
     */
    public function test_inMagicCall(Step $step, $inCallMethod)
    {
        $this->assertSame($inCallMethod, $step->inMagicCall());
    }

    public function provider_test_inMagicCall()
    {
        return array(
            array(new Step(array('class' => __CLASS__, 'function' => 'does_not_exist')), true),
            array(new Step(array('class' => __CLASS__, 'function' => __FUNCTION__)), false),
            array(new Step(array('class' => __CLASS__, 'function' => Step::CLOSURE_NAME)), false),
        );
    }

    public function test_getArguments()
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
    public function test_getFunction()
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