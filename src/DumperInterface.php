<?php

namespace ErrorDumper;

interface DumperInterface
{
    /**
     * @param \Exception|\Throwable $e
     */
    public function displayException($e);

    /**
     * @param $mode
     * @return DumperInterface
     */
    public function setMode($mode);

    /**
     * @param $output resource
     * @return DumperInterface
     */
    public function setOutputStream($output);
}