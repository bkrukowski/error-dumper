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
        var_dump($this->convertVar($var));
        ini_set(static::XDEBUG_CONFIG_KEY, $previous);
        $contents = ob_get_contents();
        ob_end_clean();

        return '<pre>' . $contents . '</pre>';
    }

    private function convertVar($var)
    {
        switch (gettype($var)) {
            case 'string':
                return $this->convertString($var);

            case 'array':
                return $this->convertArray($var);

            case 'object':
                return $this->convertObject($var);

            default:
                return $var;
        }
    }

    private function convertString($string)
    {
        // @codeCoverageIgnoreStart
        if (strlen($string) > 300) {
            $string = substr($string, 0, 300) . '...';
        }
        // @codeCoverageIgnoreStop
        return $string;
    }

    private function convertArray(array $array)
    {
        $array = array_slice($array, 0, 10, true);
        foreach ($array as &$item) {
            $item = $this->convertVar($item);
        }
        return $array;
    }

    private function convertObject($object)
    {
        $reflection = new \ReflectionClass($object);
        if ($reflection->hasMethod('__debugInfo')) {
            $method = $reflection->getMethod('__debugInfo');
            if ($method->isPublic() && !$method->getParameters()) {
                return call_user_func(array($object, '__debugInfo'));
            }
        }
        return 'object(' . get_class($object) . ') {}';
    }
}
