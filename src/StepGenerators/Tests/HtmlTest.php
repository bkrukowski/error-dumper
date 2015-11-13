<?php

namespace ErrorDumper\StepGenerators\Tests;

use ErrorDumper\DumpFunctions\InternalVarDumper;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Helpers\PHPVersion;
use ErrorDumper\StepGenerators\Html;
use ErrorDumper\StepGeneratorInterface;

/**
 * Most important thing in this test is checking if each case works, output is not tested.
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider colorizerDataProvider
     * @param \Exception|\Throwable $e
     * @param StepGeneratorInterface $stepGenerator
     */
    public function testColorizer($e, $stepGenerator)
    {
        foreach ($e->getTrace() as $step)
        {
            $colorized = $stepGenerator->prepareStep($step);
            $this->assertTrue(is_array($colorized));
            foreach (['title', 'source', 'key', 'arguments'] as $key)
            {
                $this->assertTrue(isset($colorized[$key]));
            }
            $this->assertTrue(is_array($colorized['arguments']));
        }
    }

    public function colorizerDataProvider()
    {
        $reference = 5;
        $exceptions = [
            $this->createException(),
            $this->createException('not defined in method definition'),
            $this->createExceptionInClosure(),
            $this->createExceptionRequireOnce(),
            $this->createExceptionInclude(),
            $this->createExceptionReference($reference),
        ];
        if (PHPVersion::atLeast('5.6.0'))
        {
            $exceptions[] = $this->createExceptionVariadicParams();
        }

        $result = [];
        $generators = [
            new Html(new InternalVarDumper(), new PhpStorm()),
        ];
        foreach ($generators as $generator)
        {
            foreach ($exceptions as $e)
            {
                $result[] = [$e, $generator];
            }
        }

        return $result;
    }

    private function createException()
    {
        return new \Exception();
    }

    private function createExceptionInClosure()
    {
        $fn = function () {
            return new \Exception();
        };

        return $fn();
    }

    private function createExceptionRequireOnce()
    {
        return require_once __DIR__ . DIRECTORY_SEPARATOR . 'getException.inc.php';
    }

    private function createExceptionInclude()
    {
        return include __DIR__ . DIRECTORY_SEPARATOR . 'getException.inc.php';
    }

    private function createExceptionReference(&$reference)
    {
        return new \Exception();
    }

    private function createExceptionVariadicParams()
    {
        return CreateExceptionVariadic::createException();
    }
}