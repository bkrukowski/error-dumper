<?php

namespace ErrorDumper\DumpFunctions;

use Symfony\Component;

/**
 * @codeCoverageIgnore
 */
class SymfonyVarDumper implements DumpFunctionInterface
{
    /**
     * @var Component\VarDumper\Dumper\HtmlDumper
     */
    private $dumper;

    /**
     * @var Component\VarDumper\Cloner\VarCloner
     */
    private $cloner;

    public function __construct()
    {
        $this->dumper = new Component\VarDumper\Dumper\HtmlDumper();
        $this->cloner = new Component\VarDumper\Cloner\VarCloner();
    }

    public function __invoke($var)
    {
        ob_start();
        $this->dumper->dump($this->cloner->cloneVar($var));
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
