<?php

namespace ErrorDumper\Dumpers;

use ErrorDumper\DumpFunctions\DumpFunctionInterface;
use ErrorDumper\Editors\CannotGenerateLinkException;
use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\StackTrace\Step;
use ErrorDumper\StackTrace\StepInterface;

/**
 * @internal
 */
class HtmlHelper
{
    const MAX_VARIADIC_ARGS = 5;

    /**
     * will be removed in new version
     */
    const CLOSURE_NAME = '{closure}';

    private $key = 0;

    /**
     * @var callable
     */
    private $varDump;

    /**
     * @var EditorInterface
     */
    private $editor;

    /**
     * @codeCoverageIgnore
     * @param DumpFunctionInterface $varDump
     * @param EditorInterface $editor
     */
    public function __construct(DumpFunctionInterface $varDump, EditorInterface $editor)
    {
        $this->varDump = $varDump;
        $this->editor = $editor;
    }

    private function dump($var)
    {
        return call_user_func($this->varDump, $var);
    }

    private function prepareColorizedParameters(StepInterface $step)
    {
        $params = $step->getArguments();
        if (!$params) {
            return array();
        }
        if (!$step->inFunction() || $step->inClosure() || $step->inKeywordFunction() || $step->inMagicCall()) {
            return $this->getParametersUnknown($params);
        }
        $function = $step->getFunction();
        list($result, $index, $lastParams) = $this->getParametersForFunction($function, $params);
        foreach ($lastParams as $param) {
            $dump = $this->dump($param);
            $result[] = array(
                'dump' => $dump,
                'name' => $this->getType($param) . ': unknown' . $index++,
                'full' => $this->showFull($param),
            );
        }
        return $result;
    }

    public function prepareStep(array $rawStep)
    {
        $step = new Step($rawStep);
        if (!isset($rawStep['file'])) {
            $rawStep['file'] = 'empty';
        }
        if (!isset($rawStep['line'])) {
            $rawStep['line'] = 0;
        }
        $title = "{$rawStep['file']}:{$rawStep['line']} <strong>";
        if (!empty($rawStep['class'])) {
            $title .= $rawStep['class'];
        }
        if (!empty($rawStep['type'])) {
            $title .= $rawStep['type'];
        }
        if (!empty($rawStep['function'])) {
            $title .= $rawStep['function'] . '()';
        }
        $title .= "</strong>";

        try {
            $link = $this->editor->createLinkToFile($rawStep['file'], $rawStep['line']);
            $link = htmlspecialchars($link);
            $title = "<a href='{$link}'>{$title}</a>";
        } catch (CannotGenerateLinkException $e) {
        }

        return array(
            'title' => $title,
            'source' => $this->getClassShortContents($rawStep['file'], $rawStep['line']),
            'key' => $this->key++,
            'arguments' => $this->prepareColorizedParameters($step),
        );
    }

    private function getClassShortContents($filename, $line)
    {
        if (!$line || !is_file($filename)) {
            return "File does not exists {$filename}:{$line}.";
        }
        $lines = explode("\n", file_get_contents($filename));
        $countLines = count($lines);
        $maxDiff = 7;
        $fromLine = $line - $maxDiff - 1;
        if ($fromLine < 0) {
            $fromLine = 0;
        }
        $toLine = $line + $maxDiff;
        if ($toLine > $countLines) {
            $toLine = $countLines;
        }
        $maxStrLen = strlen($toLine) + 4;
        $outputLines = array_slice($lines, $fromLine, $maxDiff*2+1, true);
        foreach ($outputLines as $key => &$currentLine) {
            $currentLine = str_replace("\t", '    ', $currentLine);
            $currentLineNumber = $key + 1;

            try {
                $lineLink = $this->editor->createLinkToFile($filename, $currentLineNumber);
                $lineLink = htmlspecialchars($lineLink);
                $lineTag = "<a href='{$lineLink}'>#{$currentLineNumber}</a>";
            } catch (CannotGenerateLinkException $e) {
                $lineTag = '#' . $currentLineNumber;
            }

            $currentLine = $lineTag
                . str_pad('', $maxStrLen - strlen($currentLineNumber), ' ')
                . htmlspecialchars($currentLine);
        }
        $outputLines[$line-1] = '<span class="error-line">' . $outputLines[$line-1] . '</span>';
        return implode("\n", $outputLines);
    }

    private function showFull($var)
    {
        if (is_scalar($var) || is_null($var)) {
            if (!is_string($var) || strlen($var) <= 30) {
                return true;
            }
        }

        if (is_array($var) && count($var) <= 10) {
            return true;
        }

        return false;
    }

    private function getType($var)
    {
        if (is_object($var)) {
            return '<strong>' . get_class($var) . '</strong>';
        }

        if (is_array($var)) {
            return 'array(' . count($var) . ')';
        }

        return \gettype($var);
    }

    private function getParametersUnknown($params)
    {
        $result = array();
        foreach ($params as $key => $param) {
            $dump = $this->dump($param);
            $result[] = array(
                'name' => $this->getType($param) . ': unknown[' . $key . ']',
                'dump' => $dump,
                'full' => $this->showFull($param),
            );
        }
        return $result;
    }

    private function getParametersForFunction(\ReflectionFunctionAbstract $function, $params)
    {
        $result = array();
        $index = 0;
        foreach ($function->getParameters() as $reflectionParam) {
            /**
             * isVariadic requires at least php 5.6
             */
            // @codeCoverageIgnoreStart
            if (version_compare(PHP_VERSION, '5.6') >= 0 && $reflectionParam->isVariadic()) {
                $result = array_merge($result, $this->getVariadicParameters($reflectionParam, $params));
                $params = array();
                break;
            }
            // @codeCoverageIgnoreEnd
            $isset = in_array(0, array_keys($params), true);
            $param = array_shift($params);
            $dump = $this->dump($param);
            $result[] = array(
                'full' => $this->showFull($param),
                'dump' => $isset ? $dump : '<pre>undefined</pre>',
                'name' => $this->getType($param) . ': '
                    . ($reflectionParam->isPassedByReference() ? '&' : '') . '$' . $reflectionParam->getName(),
            );
            $index++;
        }
        return array($result, $index, $params);
    }

    private function getVariadicParameters(\ReflectionParameter $reflectionParam, array $params)
    {
        $result = array();

        if (empty($params) || count($params) > static::MAX_VARIADIC_ARGS) {
            $dump = $this->dump($params);
            $result[] = array(
                'name' => '...$' . $reflectionParam->getName(),
                'dump' => $dump,
                'full' => $this->showFull($params),
            );
            return $result;
        }

        foreach ($params as $i => $param) {
            $dump = $this->dump($param);
            $result[] = array(
                'name' => $this->getType($param) . ': ...$' . $reflectionParam->getName() . '[' . $i . ']',
                'dump' => $dump,
                'full' => $this->showFull($param),
            );
        }
        return $result;
    }
}
