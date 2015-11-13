<?php

namespace ErrorDumper\DumpFunctions;

use ErrorDumper\DumpFunctionInterface;

class DetectVarDumper
{
    const SYMFONY_COMPONENT_CLASS = 'Symfony\Component\VarDumper\Dumper\HtmlDumper';

    /**
     * @codeCoverageIgnore
     * @return DumpFunctionInterface
     */
    public static function createVarDumper()
    {
        if (class_exists(static::SYMFONY_COMPONENT_CLASS, true))
        {
            return new SymfonyVarDumper();
        }

        return new InternalVarDumper();
    }
}