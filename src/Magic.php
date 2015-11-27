<?php

namespace ErrorDumper;

use ErrorDumper\Editors;
use ErrorDumper\Handlers\Handler;
use ErrorDumper\Handlers\HandlerInterface;
use ErrorDumper\Handlers\RegisterErrorHandler;
use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\Dumpers;

/**
 * @codeCoverageIgnore
 *
 * registerErrorCallback() and registerErrorDumper() are mutually exclusive
 */
class Magic
{
    /**
     * If you want skip some errors, call:
     * Magic::registerErrorDumper()->setPreCallback(callable $preCallback);
     *
     * If you want save information about error, call:
     * Magic::registerErrorDumper()->setPostCallback(callable $postCallback);
     *
     * Chain syntax is supported, for example:
     * Magic::registerErrorDumper()
     *  ->setPreCallback(callable $preCallback)
     *  ->setPostCallback(callable $postCallback);
     *
     * @see Handler::__invoke()
     * @param Editors\EditorInterface|null $editor
     * @return HandlerInterface
     */
    public static function registerErrorDumper(Editors\EditorInterface $editor = null)
    {
        $preCallback = null;
        $postCallback = function () {
            exit(1);
        };
        if (php_sapi_name() === 'cli')
        {
            $dumper = new Dumpers\Cli(fopen('php:/output', 'w'));
        }
        else
        {
            is_null($editor) && $editor = new Editors\PhpStorm();
            $dumper = (new Dumpers\Html($editor, fopen('php://output', 'w')));
            $preCallback = function () {
                if (!headers_sent())
                {
                    header('HTTP/1.1 503 Service Temporarily Unavailable');
                    header('Status: 503 Service Temporarily Unavailable');
                    header('Retry-After: 300');
                }
            };
        }
        $handler = new Handler($dumper);
        $handler->setPostCallback($postCallback);
        if ($preCallback)
        {
            $handler->setPreCallback($preCallback);
        }
        $registerErrorHandler = new RegisterErrorHandler($handler);
        $registerErrorHandler->register();

        return $handler;
    }

    /**
     * Method only for catching errors, without friendly output.
     *
     * @param $callable
     * @param int $mode
     * @throws Helpers\NotCallableException
     */
    public static function registerErrorCallback($callable, $mode = null)
    {
        if (is_null($mode))
        {
            $mode = E_STRICT | E_ALL;
        }
        Exceptions::throwIfIsNotCallable($callable);
        $registerErrorHandler = new RegisterErrorHandler($callable, $mode);
        $registerErrorHandler->register();
    }
}