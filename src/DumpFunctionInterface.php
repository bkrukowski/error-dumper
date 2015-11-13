<?php

namespace ErrorDumper;

interface DumpFunctionInterface
{
    /**
     * @param $var
     * @return string
     */
    public function __invoke($var);
}