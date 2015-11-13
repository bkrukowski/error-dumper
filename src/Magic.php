<?php

namespace ErrorDumper;

use ErrorDumper\Editors;
use ErrorDumper\Helpers\Exceptions;

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
     * @param EditorInterface|null $editor
     * @return HandlerInterface
     */
    public static function registerErrorDumper(EditorInterface $editor = null)
    {
        is_null($editor) && $editor = new Editors\PhpStorm();
        $cliModes = ['cli'];
        $mode = in_array(php_sapi_name(), $cliModes) ? Dumper::MODE_CLI : Dumper::MODE_HTML;
        $dumper = (new Dumper())
            ->setMode($mode)
            ->setOutputStream(fopen('php://output', 'w'))
            ->setEditor($editor)
            ->setBootstrapJs(Dumper::BOOTSTRAP_JS)
            ->setBootstrapCss(Dumper::BOOTSTRAP_CSS)
            ->setJqueryJs(Dumper::JQUERY_JS);
        $preCallback = function () use ($mode) {
            if ($mode === Dumper::MODE_HTML && !headers_sent())
            {
                http_response_code(503);
            }
        };
        $postCallback = function () {
            exit(1);
        };
        $handler = (new Handler($dumper))
            ->setPreCallback($preCallback)
            ->setPostCallback($postCallback);
        (new RegisterErrorHandler($handler))->register();

        return $handler;
    }

    /**
     * Method only for catching errors, without friendly output.
     *
     * @param $callable
     * @param int $mode
     * @throws Helpers\NotCallableException
     */
    public static function registerErrorCallback($callable, $mode = E_STRICT | E_ALL)
    {
        Exceptions::throwIfIsNotCallable($callable);
        (new RegisterErrorHandler($callable, $mode))->register();
    }
}