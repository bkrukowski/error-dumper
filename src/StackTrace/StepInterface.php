<?php

namespace ErrorDumper\StackTrace;

/**
 * @internal
 */
interface StepInterface
{
    /**
     * @return bool
     */
    public function inFunction();

    /**
     * @return bool
     */
    public function inClosure();

    /**
     * @return bool
     */
    public function inKeywordFunction();

    /**
     * @return bool
     */
    public function inClass();

    /**
     * @return bool
     */
    public function inMagicCall();

    /**
     * @return array
     */
    public function getArguments();

    /**
     * @return \ReflectionFunctionAbstract
     * @throws NoFunctionException
     */
    public function getFunction();
}
