<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\HtmlHelper;
use ErrorDumper\DumpFunctions\InternalVarDumper;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Helpers\PHPVersion;

class HtmlHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_test_prepareStep
     * @param HtmlHelper $helper
     * @param \Exception|\Throwable $e
     */
    public function test_prepareStep(HtmlHelper $helper, $e)
    {
        foreach ($e->getTrace() as $step)
        {
            $colorized = $helper->prepareStep($step);
            $this->assertTrue(is_array($colorized));
            foreach (array('title', 'source', 'key', 'arguments') as $key)
            {
                $this->assertTrue(isset($colorized[$key]));
            }
            $this->assertTrue(is_array($colorized['arguments']));
        }
    }

    public function provider_test_prepareStep()
    {
        $reference = 5;
        $exceptions = array(
            $this->createException(),
            $this->createException('not defined in method definition'),
            $this->createExceptionInClosure(),
            $this->createExceptionRequireOnce(),
            $this->createExceptionInclude(),
            $this->createExceptionReference($reference),
        );
        if (PHPVersion::atLeast('5.6.0'))
        {
            $exceptions[] = $this->createExceptionVariadicParams();
        }

        $result = array();
        foreach ($exceptions as $exception)
        {
            $result[] = array(
                new HtmlHelper(new InternalVarDumper(), new PhpStorm()),
                $exception,
            );
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