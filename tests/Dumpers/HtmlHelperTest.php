<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\HtmlHelper;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\Editors\PhpStorm;

class HtmlHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestPrepareStep
     * @param HtmlHelper $helper
     * @param \Exception|\Throwable $exception
     */
    public function testPrepareStep(HtmlHelper $helper, $exception)
    {
        foreach ($exception->getTrace() as $step) {
            $colorized = $helper->prepareStep($step);
            $this->assertTrue(is_array($colorized));
            foreach (array('title', 'source', 'key', 'arguments') as $key) {
                $this->assertTrue(isset($colorized[$key]));
            }
            $this->assertTrue(is_array($colorized['arguments']));
        }
    }

    public function providerTestPrepareStep()
    {
        $reference = 5;
        $exceptionsHelper = new HtmlHelperExceptions();
        $exceptions = array(
            $exceptionsHelper->createException(),
            $exceptionsHelper->createException('not defined in method definition'),
            $exceptionsHelper->createExceptionInClosure(),
            $exceptionsHelper->createExceptionRequireOnce(),
            $exceptionsHelper->createExceptionInclude(),
            $exceptionsHelper->createExceptionReference($reference),
            $exceptionsHelper->createExceptionMagicCall(),
            $exceptionsHelper->createExceptionMagicStaticCall(),
        );
        if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
            $exceptions[] = $exceptionsHelper->createExceptionVariadicParams();
            $exceptions[] = $exceptionsHelper->createExceptionVariadicParams2();
        }

        $result = array();
        foreach ($exceptions as $exception) {
            $result[] = array(
                new HtmlHelper(new LightVarDumper(), new PhpStorm()),
                $exception,
            );
        }

        return $result;
    }
}
