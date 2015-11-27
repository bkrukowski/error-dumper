<?php

namespace ErrorDumper\Handlers;

interface CallbackEventInterface
{
    /**
     * @param \Exception|\Throwable $e
     */
    public function __invoke($e);
}