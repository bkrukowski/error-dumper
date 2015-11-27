<?php

namespace ErrorDumper\Handlers;

interface RegisterErrorHandlerInterface
{
    /**
     * @param callable $callable
     * @return RegisterErrorHandlerInterface
     */
    public function setCallable($callable);

    /**
     * @return RegisterErrorHandlerInterface
     */
    public function register();
}