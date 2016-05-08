<?php

namespace ErrorDumper\Dumpers;

abstract class Base implements DumperInterface
{
    protected $outputStream;

    public function setOutputStream($output)
    {
        $this->outputStream = $output;

        return $this;
    }
}
