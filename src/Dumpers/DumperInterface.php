<?php

namespace ErrorDumper\Dumpers;

interface DumperInterface
{
    /**
     * @param \Exception|\Throwable $e
     */
    public function displayException($e);

    /**
     * @param $output resource
     * @return DumperInterface
     */
    public function setOutputStream($output);
}