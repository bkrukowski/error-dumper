<?php

namespace ErrorDumper\Tests\Dumpers;

class HtmlHelperExceptions
{
    public function createException()
    {
        return new \Exception();
    }

    public function createExceptionInClosure()
    {
        $closure = function () {
            return new \Exception();
        };

        return $closure();
    }

    public function createExceptionRequireOnce()
    {
        return require_once __DIR__ . DIRECTORY_SEPARATOR . 'getException.inc.php';
    }

    public function createExceptionInclude()
    {
        return include __DIR__ . DIRECTORY_SEPARATOR . 'getException.inc.php';
    }

    public function createExceptionReference(&$reference)
    {
        $reference = 'foo';
        return new \Exception();
    }

    public function createExceptionVariadicParams()
    {
        return CreateExceptionVariadic::createException();
    }

    public function createExceptionVariadicParams2()
    {
        return CreateExceptionVariadic::createException2();
    }

    public function createExceptionMagicCall()
    {
        $obj = new CreateExceptionMagicCall();
        return $obj->exception('foo', 'bar');
    }

    public function createExceptionMagicStaticCall()
    {
        return CreateExceptionMagicCall::staticException('foo', 'bar');
    }
}