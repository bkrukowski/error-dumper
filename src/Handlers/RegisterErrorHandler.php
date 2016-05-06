<?php

namespace ErrorDumper\Handlers;

use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\StandardExceptions\FatalErrorException;
use ErrorDumper\StandardExceptions\ShutdownErrorException;

/**
 * @codeCoverageIgnore
 */
class RegisterErrorHandler implements RegisterErrorHandlerInterface
{
    const TYPE_ERRORS = 1;
    const TYPE_EXCEPTIONS = 2;
    const TYPE_SHUTDOWN_ERRORS = 4;
    const TYPE_ALL = 7;

    /**
     * @var callable
     */
    private $callable;

    private $mode;

    /**
     * @param callable $callable
     * @param int $mode
     */
    public function __construct($callable, $mode = null)
    {
        if (is_null($mode))
        {
            $mode = E_STRICT | E_ALL;
        }
        $this->setCallable($callable);
        $this->mode = $mode;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function register($errorTypes = RegisterErrorHandler::TYPE_ALL)
    {
        ($errorTypes & static::TYPE_EXCEPTIONS) && $this->registerExceptionHandler();
        ($errorTypes & static::TYPE_ERRORS) && $this->registerErrorHandler();
        ($errorTypes & static::TYPE_SHUTDOWN_ERRORS) && $this->registerShutdownHandler();

        return $this;
    }

    public function setCallable($callable)
    {
        Exceptions::throwIfIsNotCallable($callable);
        $this->callable = $callable;

        return $this;
    }

    /**
     * This method is public because of supporting php 5.3.
     * @param $exception
     */
    public function onError($exception)
    {
        Exceptions::throwIfIsNotThrowable($exception);
        call_user_func($this->callable, $exception);
    }

    private function registerExceptionHandler()
    {
        $self = $this;
        set_exception_handler(function ($exception) use ($self) {
            $self->onError($exception);
        });
    }

    private function registerErrorHandler()
    {
        $self = $this;
        set_error_handler(function ($no, $str, $file, $line) use ($self) {
            $exception = new FatalErrorException();
            $exception
                ->setFile($file)
                ->setMessage($str)
                ->setCode($no)
                ->setLine($line);
            $self->onError($exception);
        }, $this->mode);
    }

    private function registerShutdownHandler()
    {
        $self = $this;
        register_shutdown_function(function () use ($self) {
            if ($error = error_get_last())
            {
                $exception = new ShutdownErrorException();
                $exception
                    ->setFile($error['file'])
                    ->setMessage($error['message'])
                    ->setCode($error['type'])
                    ->setLine($error['line']);
                $self->onError($exception);
            }
        });
    }
}