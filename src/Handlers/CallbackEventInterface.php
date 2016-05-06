<?php

namespace ErrorDumper\Handlers;

interface CallbackEventInterface
{
    /**
     * @param \Exception|\Throwable $exception
     */
    public function __invoke($exception);
}