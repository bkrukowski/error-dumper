<?php

namespace ErrorDumper;

use ErrorDumper\DumpFunctions\DumpFunctionInterface;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\Editors;
use ErrorDumper\Handlers\Handler;
use ErrorDumper\Handlers\HandlerInterface;
use ErrorDumper\Handlers\RegisterErrorHandler;
use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\Dumpers;
use ErrorDumper\Helpers\Stream;

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
     * @param int $errorTypes
     * @return HandlerInterface
     */
    public static function registerErrorDumper(
        Editors\EditorInterface $editor = null,
        $errorTypes = RegisterErrorHandler::TYPE_ALL
    ) {
        $dumper = self::createDumperBySapiName($editor);
        $handler = new Handler($dumper);
        $handler->setPostCallback(function () {
            exit(1);
        });
        if (php_sapi_name() !== 'cli') {
            $handler->setPreCallback(function () {
                if (!headers_sent()) {
                    header('HTTP/1.1 503 Service Temporarily Unavailable');
                    header('Status: 503 Service Temporarily Unavailable');
                    header('Retry-After: 300');
                }
            });
        }
        $registerErrorHandler = new RegisterErrorHandler($handler);
        $registerErrorHandler->register($errorTypes);

        return $handler;
    }

    /**
     * Method only for catching errors, without friendly output.
     *
     * @param $callable
     * @param int|null $mode
     * @param int $errorTypes
     * @throws Helpers\NotCallableException
     */
    public static function registerErrorCallback($callable, $mode = null, $errorTypes = RegisterErrorHandler::TYPE_ALL)
    {
        $exceptions = new Exceptions();
        $exceptions->throwIfIsNotCallable($callable);
        $registerErrorHandler = new RegisterErrorHandler($callable, $mode);
        $registerErrorHandler->register($errorTypes);
    }

    /**
     * @param $exception
     * @param Editors\EditorInterface|null $editor
     * @param DumpFunctionInterface $varDumper
     * @return string
     * @throws Helpers\NotThrowableException
     */
    public static function exportExceptionToLightHtml(
        $exception,
        Editors\EditorInterface $editor = null,
        DumpFunctionInterface $varDumper = null
    ) {
        $exceptions = new Exceptions();
        $exceptions->throwIfIsNotThrowable($exception);
        is_null($editor) && $editor = new Editors\Nothing();
        $tmp = tmpfile();
        $dumper = new Dumpers\Html($editor, $tmp);
        if (is_null($varDumper)) {
            $varDumper = new LightVarDumper();
        }
        $dumper->setVarDumpFn($varDumper);
        $dumper->displayException($exception);
        $stream = new Stream($tmp);
        $result = $stream->getContents();
        fclose($tmp);

        return $result;
    }

    /**
     * @param Editors\EditorInterface|null $editor
     * @return Dumpers\DumperInterface
     */
    private static function createDumperBySapiName(Editors\EditorInterface $editor = null)
    {
        if (php_sapi_name() ==='cli') {
            $dumper = new Dumpers\Cli(fopen('php://output', 'w'));
            $dumper->setWindowWidthGetter(function () {
                return (int) exec('tput cols') ? : Dumpers\Cli::EMPHASIS_LENGTH;
            });

            return $dumper;
        }

        is_null($editor) && $editor = new Editors\PhpStorm();
        return new Dumpers\Html($editor, fopen('php://output', 'w'));
    }
}
