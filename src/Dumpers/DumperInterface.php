<?php

namespace ErrorDumper\Dumpers;

interface DumperInterface
{
    /**
     * @param \Exception|\Throwable $exception
     */
    public function displayException($exception);

    /**
     * @param $output resource
     * @return DumperInterface
     */
    public function setOutputStream($output);
}
