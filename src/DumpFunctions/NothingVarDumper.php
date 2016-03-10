<?php

namespace ErrorDumper\DumpFunctions;

class NothingVarDumper implements DumpFunctionInterface
{
    public function __invoke($var)
    {
        return '<pre>empty dump of variable</pre>';
    }
}