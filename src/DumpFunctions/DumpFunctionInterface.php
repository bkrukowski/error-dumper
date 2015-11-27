<?php

namespace ErrorDumper\DumpFunctions;

interface DumpFunctionInterface
{
    /**
     * @param $var
     * @return string
     */
    public function __invoke($var);
}