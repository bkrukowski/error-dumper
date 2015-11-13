<?php

namespace ErrorDumper;

use ErrorDumper\Helpers\Exceptions;
use ErrorDumper\StepGenerators;
use ErrorDumper\DumpFunctions;

class Dumper implements DumperInterface
{
    const TAG_HTML = '<!-- @ErrorDumper -->';
    const MODE_CLI = 'cli';
    const MODE_HTML = 'html';
    const ALLOWED_MODES = [
        self::MODE_CLI,
        self::MODE_HTML,
    ];
    const BOOTSTRAP_CSS = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css';
    const BOOTSTRAP_JS = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js';
    const JQUERY_JS = '//code.jquery.com/jquery-2.1.4.min.js';

    private $mode = self::MODE_HTML;

    private $bootstrapCss = '';
    private $bootstrapJs = '';
    private $jqueryJs = '';

    private $output;

    /**
     * @var EditorInterface
     */
    private $editor;

    /**
     * @codeCoverageIgnore
     * @return StepGeneratorInterface
     */
    private function createStepGenerator()
    {
        switch ($this->mode)
        {
            case static::MODE_HTML:
                return new StepGenerators\Html(DumpFunctions\DetectVarDumper::createVarDumper(), $this->editor);

            case static::MODE_CLI:
                return new StepGenerators\Cli();
        }
    }

    /**
     * @param \Exception|\Throwable $e
     * @return array
     */
    private function getTemplateDataForException($e)
    {
        $result = [
            'exception' => $e,
            'exceptionClass' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => [],
        ];
        $stepGenerator = $this->createStepGenerator();
        $result['trace'][] = $stepGenerator->prepareStep([
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        foreach ($e->getTrace() as $rawStep)
        {
            $result['trace'][] = $stepGenerator->prepareStep($rawStep);
        }

        if ($this->mode === static::MODE_HTML)
        {
            $result['__static'] = [
                'css' => [
                    $this->bootstrapCss,
                ],
                'js' => [
                    $this->jqueryJs,
                    $this->bootstrapJs,
                ],
            ];
        }

        return $result;
    }

    public function displayException($e)
    {
        Exceptions::throwIfIsNotThrowable($e);
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'templates', 'exceptions', $this->mode . '.php']);
        $data = $this->getTemplateDataForException($e);
        ob_start();
        require $path;
        $contents = ob_get_contents();
        ob_end_clean();
        fwrite($this->output, $contents);
    }

    public function setMode($mode)
    {
        if (!in_array($mode, static::ALLOWED_MODES, true))
        {
            throw new DumperException('Invalid mode: ' . var_export($mode, true));
        }
        $this->mode = $mode;

        return $this;
    }

    public function setOutputStream($output)
    {
        $this->output = $output;

        return $this;
    }

    public function setEditor(EditorInterface $editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @param $path
     * @return Dumper
     */
    public function setBootstrapCss($path)
    {
        $this->bootstrapCss = $path;

        return $this;
    }

    /**
     * @param $path
     * @return Dumper
     */
    public function setBootstrapJs($path)
    {
        $this->bootstrapJs = $path;

        return $this;
    }

    /**
     * @param $path
     * @return Dumper
     */
    public function setJqueryJs($path)
    {
        $this->jqueryJs = $path;

        return $this;
    }
}