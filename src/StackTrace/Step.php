<?php

namespace ErrorDumper\StackTrace;

/**
 * @internal
 */
class Step implements StepInterface
{
    const CLOSURE_NAME = '{closure}';

    private $rawStep;

    public function __construct(array $rawStep)
    {
        $this->rawStep = $rawStep;
    }

    public function inFunction()
    {
        return isset($this->rawStep['function']);
    }

    public function inClosure()
    {
        return $this->inFunction() && strpos($this->rawStep['function'], static::CLOSURE_NAME) !== false;
    }

    public function inKeywordFunction()
    {
        return $this->inFunction() && !$this->inClass() && !function_exists($this->rawStep['function']);
    }

    public function inClass()
    {
        return isset($this->rawStep['class']);
    }

    public function inMagicCall()
    {
        return $this->inClass()
            && !$this->inClosure()
            && !method_exists($this->rawStep['class'], $this->rawStep['function']);
    }

    public function getArguments()
    {
        return isset($this->rawStep['args']) ? $this->rawStep['args'] : array();
    }

    public function getFunction()
    {
        if ($this->inClass() && !$this->inMagicCall()) {
            $class = new \ReflectionClass($this->rawStep['class']);
            return $class->getMethod($this->rawStep['function']);
        }

        if (!$this->inClass() && !$this->inKeywordFunction() && !$this->inClosure() && $this->inFunction()) {
            return new \ReflectionFunction($this->rawStep['function']);
        }

        throw new NoFunctionException('Cannot prepare function for step ' . print_r($this->rawStep, true));
    }
}
