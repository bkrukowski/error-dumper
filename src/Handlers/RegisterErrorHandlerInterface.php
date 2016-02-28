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
     * @param int $errorTypes
     * @return RegisterErrorHandlerInterface
     */
    public function register($errorTypes = RegisterErrorHandler::TYPE_ALL);
}