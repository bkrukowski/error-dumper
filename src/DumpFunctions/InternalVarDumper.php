<?php

namespace ErrorDumper\DumpFunctions;

class InternalVarDumper implements DumpFunctionInterface
{
    public function __invoke($var)
    {
        ob_start();
        var_dump($var);
        $result = ob_get_contents();
        ob_end_clean();
        $xdebugIsLoaded = extension_loaded('xdebug');

        return ($xdebugIsLoaded ? '' : '<pre>') . $result . ($xdebugIsLoaded ? '' : '</pre>');
    }
}
