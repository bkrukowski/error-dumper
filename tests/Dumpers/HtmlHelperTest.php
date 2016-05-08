<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\HtmlHelper;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Helpers\PHPVersion;
use ErrorDumper\Tests\TestBase;

class HtmlHelperTest extends TestBase
{
    /**
     * @dataProvider provider_test_prepareStep
     * @param HtmlHelper $helper
     * @param \Exception|\Throwable $exception
     */
    public function test_prepareStep(HtmlHelper $helper, $exception)
    {
        foreach ($exception->getTrace() as $step)
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
            $this->createExceptionMagicCall(),
            $this->createExceptionMagicStaticCall(),
        );
        if (PHPVersion::atLeast('5.6.0'))
        {
            $exceptions[] = $this->createExceptionVariadicParams();
            $exceptions[] = $this->createExceptionVariadicParams2();
        }

        $result = array();
        foreach ($exceptions as $exception)
        {
            $result[] = array(
                new HtmlHelper(new LightVarDumper(), new PhpStorm()),
                $exception,
            );
        }

        return $this->prepareDataProvider($result);
    }

    private function createException()
    {
        return new \Exception();
    }

    private function createExceptionInClosure()
    {
        $closure = function () {
            return new \Exception();
        };

        return $closure();
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
        $reference = 'foo';
        return new \Exception();
    }

    private function createExceptionVariadicParams()
    {
        return CreateExceptionVariadic::createException();
    }

    private static function createExceptionVariadicParams2()
    {
        return CreateExceptionVariadic::createException2();
    }

    private function createExceptionMagicCall()
    {
        $obj = new CreateExceptionMagicCall();
        return $obj->exception('foo', 'bar');
    }

    public static function createExceptionMagicStaticCall()
    {
        return CreateExceptionMagicCall::staticException('foo', 'bar');
    }
}