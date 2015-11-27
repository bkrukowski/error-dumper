<?php

namespace ErrorDumper\DumpFunctions;

class Detector
{
    const SYMFONY_COMPONENT_CLASS = 'Symfony\Component\VarDumper\Dumper\HtmlDumper';

    /**
     * @codeCoverageIgnore
     * @return DumpFunctionInterface
     */
    public static function createDetectedVarDumper()
    {
        if (class_exists(static::SYMFONY_COMPONENT_CLASS, true))
        {
            return new SymfonyVarDumper();
        }

        return new InternalVarDumper();
    }
}