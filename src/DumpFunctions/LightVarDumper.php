<?php

namespace ErrorDumper\DumpFunctions;

class LightVarDumper implements DumpFunctionInterface
{
    const XDEBUG_CONFIG_KEY = 'xdebug.overload_var_dump';

    public function __invoke($var)
    {
        ob_start();
        $previous = ini_get(static::XDEBUG_CONFIG_KEY);
        ini_set(static::XDEBUG_CONFIG_KEY, false);
        var_dump($var);
        ini_set(static::XDEBUG_CONFIG_KEY, $previous);
        $contents = ob_get_contents();
        ob_end_clean();

        return '<pre>' . substr($contents, 0, 300) . '</pre>';
    }
}
