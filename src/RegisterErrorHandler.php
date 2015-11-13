<?php

namespace ErrorDumper;
use ErrorDumper\Helpers\Exceptions;

/**
 * @codeCoverageIgnore
 */
class RegisterErrorHandler implements RegisterErrorHandlerInterface
{
    /**
     * @var callable
     */
    private $callable;

    private $mode;

    /**
     * @param callable $callable
     * @param int $mode
     */
    public function __construct($callable, $mode = E_STRICT | E_ALL)
    {
        $this->setCallable($callable);
        $this->mode = $mode;
    }

    /**
     * @codeCoverageIgnore
     */
    public function register()
    {
        $this->registerExceptionHandler();
        $this->registerErrorHandler();
        $this->registerShutdownHandler();

        return $this;
    }

    public function setCallable($callable)
    {
        Exceptions::throwIfIsNotCallable($callable);
        $this->callable = $callable;

        return $this;
    }

    private function onError($e)
    {
        call_user_func($this->callable, $e);
    }

    private function registerExceptionHandler()
    {
        set_exception_handler(function ($e) {
            $this->onError($e);
        });
    }

    private function registerErrorHandler()
    {
        set_error_handler(function ($no, $str, $file, $line) {
            $e = (new FatalErrorException())
                ->setFile($file)
                ->setMessage($str)
                ->setCode($no)
                ->setLine($line);
            $this->onError($e);
        }, $this->mode);
    }

    private function registerShutdownHandler()
    {
        register_shutdown_function(function () {
            if ($error = error_get_last())
            {
                $e = (new ShutdownErrorException())
                    ->setFile($error['file'])
                    ->setMessage($error['message'])
                    ->setCode($error['type'])
                    ->setLine($error['line']);
                $this->onError($e);
            }
        });
    }
}