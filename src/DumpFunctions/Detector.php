<?php

namespace ErrorDumper\DumpFunctions;

/**
 * @deprecated
 */
class Detector
{
    const SYMFONY_COMPONENT_CLASS = 'Symfony\Component\VarDumper\Dumper\HtmlDumper';

    /**
     * @deprecated this method will not be static in major new version
     * @codeCoverageIgnore
     * @return DumpFunctionInterface
     */
    public static function createDetectedVarDumper()
    {
        if (class_exists(static::SYMFONY_COMPONENT_CLASS, true)) {
            return new SymfonyVarDumper();
        }

        return new InternalVarDumper();
    }
}
