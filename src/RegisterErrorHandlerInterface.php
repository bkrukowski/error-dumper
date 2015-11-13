<?php

namespace ErrorDumper;

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