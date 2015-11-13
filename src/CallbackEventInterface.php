<?php

namespace ErrorDumper;

interface CallbackEventInterface
{
    /**
     * @param \Exception|\Throwable $e
     */
    public function __invoke($e);
}