<?php

namespace ErrorDumper\Dumpers;

use ErrorDumper\DumpFunctions\Detector;
use ErrorDumper\DumpFunctions\DumpFunctionInterface;
use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\Views\Template;

class Html extends Base
{
    const TAG_HTML = '<!-- @ErrorDumper -->';
    const TAG_UNDER_TITLE = '<!-- @ErrorDumper under title -->';

    const BOOTSTRAP_CSS = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css';
    const BOOTSTRAP_JS = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js';
    const JQUERY_JS = '//code.jquery.com/jquery-2.1.4.min.js';

    private $bootstrapCss = '';
    private $bootstrapJs = '';
    private $jqueryJs = '';

    /**
     * @var EditorInterface
     */
    private $editor;

    /**
     * @var callable
     */
    private $varDumpFn;

    public function __construct(EditorInterface $editor, $stream)
    {
        $detector = new Detector();
        $this
            ->setEditor($editor)
            ->setOutputStream($stream)
            ->setBootstrapJs(static::BOOTSTRAP_JS)
            ->setBootstrapCss(static::BOOTSTRAP_CSS)
            ->setJqueryJs(static::JQUERY_JS)
            ->setVarDumpFn($detector->createDetectedVarDumper());
    }

    public function displayException($exception)
    {
        $exceptions = new Exceptions();
        $exceptions->throwIfIsNotThrowable($exception);
        $vars = array(
            'exception' => $exception,
            'exceptionClass' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => array(),
            '__static' => array(
                'css' => array(
                    $this->bootstrapCss,
                ),
                'js' => array(
                    $this->jqueryJs,
                    $this->bootstrapJs,
                ),
            ),
        );
        $helper = new HtmlHelper($this->varDumpFn, $this->editor);
        $vars['trace'][] = $helper->prepareStep(array(
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ));
        foreach ($exception->getTrace() as $rawStep) {
            $vars['trace'][] = $helper->prepareStep($rawStep);
        }
        $template = new Template();
        fputs($this->outputStream, $template->render('exceptions/html', $vars));
    }

    /**
     * @param EditorInterface $editor
     * @return Html
     */
    public function setEditor(EditorInterface $editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @param DumpFunctionInterface $function
     * @return Html
     * @throws \ErrorDumper\Helpers\NotCallableException
     */
    public function setVarDumpFn(DumpFunctionInterface $function)
    {
        Exceptions::throwIfIsNotCallable($function);
        $this->varDumpFn = $function;

        return $this;
    }

    /**
     * @param $path
     * @return Html
     */
    public function setBootstrapCss($path)
    {
        $this->bootstrapCss = $path;

        return $this;
    }

    /**
     * @param $path
     * @return Html
     */
    public function setBootstrapJs($path)
    {
        $this->bootstrapJs = $path;

        return $this;
    }

    /**
     * @param $path
     * @return Html
     */
    public function setJqueryJs($path)
    {
        $this->jqueryJs = $path;

        return $this;
    }
}
