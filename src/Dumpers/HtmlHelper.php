<?php

namespace ErrorDumper\Dumpers;

use ErrorDumper\DumpFunctions\DumpFunctionInterface;
use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Helpers\PHPVersion;

class HtmlHelper
{
    const MAX_VARIADIC_ARGS = 5;
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

    private function prepareColorizedParameters($step)
    {
        $params = isset($step['args']) ? $step['args'] : array();
        if (!$params)
        {
            return array();
        }
        if (!isset( $step['function'] ) || strpos($step['function'], static::CLOSURE_NAME) !== false)
        {
            $result = array();
            foreach ($params as $key => $param)
            {
                $dump = $this->dump($param);
                $result[] = array(
                    'name' => $this->getType($param) . ': unknown[' . $key . ']',
                    'dump' => $dump,
                    'full' => $this->showFull($param, $dump),
                );
            }
            return $result;
        }
        if (!isset( $step['class'] ))
        {
            if (!function_exists($step['function'])) # for example include
            {
                $result = array();
                foreach ($params as $key => $param)
                {
                    $dump = $this->dump($param);
                    $result[] = array(
                        'name' => $this->getType($param) . ': unknown[' . $key . ']',
                        'dump' => $dump,
                        'full' => $this->showFull($param, $dump),
                    );
                }
                return $result;
            }

            $function = new \ReflectionFunction($step['function']);
        }
        else
        {
            $class = new \ReflectionClass($step['class']);
            if (!$class->hasMethod($step['function']))
            {
                $result = array();
                foreach ($params as $key => $param)
                {
                    $dump = $this->dump($param);
                    $result[] = array(
                        'name' => $this->getType($param) . ': unknown[' . $key . ']',
                        'dump' => $dump,
                        'full' => $this->showFull($param, $dump),
                    );
                }
                return $result;
            }
            $function = $class->getMethod($step['function']);
        }
        $result = array();
        $index = 0;
        foreach ($function->getParameters() as $reflectionParam)
        {
            /**
             * isVariadic requires at least php 5.6.0
             */
            if (PHPVersion::atLeast('5.6.0') && $reflectionParam->isVariadic())
            {
                if (empty($params) || count($params) > static::MAX_VARIADIC_ARGS)
                {
                    $dump = $this->dump($params);
                    $result[] = array(
                        'name' => '...$' . $reflectionParam->getName(),
                        'dump' => $dump,
                        'full' => $this->showFull($params, $dump),
                    );
                }
                else
                {
                    foreach ($params as $i => $param)
                    {
                        $dump = $this->dump($param);
                        $result[] = array(
                            'name' => $this->getType($param) . ': ...$' . $reflectionParam->getName() . '[' . $i . ']',
                            'dump' => $dump,
                            'full' => $this->showFull($param, $dump),
                        );
                    }
                }
                $params = array();
                break;
            }
            $isset = in_array(0, array_keys($params), true);
            $param = array_shift($params);
            $dump = $this->dump($param);
            $result[] = array(
                'full' => $this->showFull($param, $dump),
                'dump' => $isset ? $dump : '<pre>undefined</pre>',
                'name' => $this->getType($param) . ': ' . ($reflectionParam->isPassedByReference() ? '&' : '') . '$' . $reflectionParam->getName(),
            );
            $index++;
        }
        foreach ($params as $param)
        {
            $dump = $this->dump($param);
            $result[] = array(
                'dump' => $dump,
                'name' => $this->getType($param) . ': unknown' . $index++,
                'full' => $this->showFull($param, $dump),
            );
        }
        return $result;
    }

    public function prepareStep(array $rawStep)
    {
        if (!isset($rawStep['file']))
        {
            $rawStep['file'] = 'empty';
        }
        if (!isset($rawStep['line']))
        {
            $rawStep['line'] = 0;
        }
        $title = "{$rawStep['file']}:{$rawStep['line']} <strong>";
        if (!empty($rawStep['class']))
        {
            $title .= $rawStep['class'];
        }
        if (!empty($rawStep['type']))
        {
            $title .= $rawStep['type'];
        }
        if (!empty($rawStep['function']))
        {
            $title .= $rawStep['function'] . '()';
        }
        $title .= "</strong>";
        $link = $this->editor->createLinkToFile($rawStep['file'], $rawStep['line']);
        if ($link !== '')
        {
            $link = htmlspecialchars($link);
            $title = "<a href='{$link}'>{$title}</a>";
        }

        return array(
            'title' => $title,
            'source' => $this->getClassShortContents($rawStep['file'], $rawStep['line']),
            'key' => $this->key++,
            'arguments' => $this->prepareColorizedParameters($rawStep),
        );
    }

    private function getClassShortContents($filename, $line)
    {
        if (!is_file($filename))
        {
            return "File does not exists {$filename}:{$line}.";
        }
        $lines = explode("\n", file_get_contents($filename));
        $countLines = count($lines);
        $maxDiff = 7;
        $from = $line - $maxDiff - 1;
        if ($from < 0)
        {
            $from = 0;
        }
        $to = $line + $maxDiff;
        if ($to > $countLines)
        {
            $to = $countLines;
        }
        $maxStrLen = strlen($to) + 4;
        $outputLines = array_slice($lines, $from, $maxDiff*2+1, true);
        foreach ($outputLines as $key => &$currentLine)
        {
            $currentLine = str_replace("\t", '    ', $currentLine);
            $currentLineNumber = $key + 1;
            $lineLink = $this->editor->createLinkToFile($filename, $currentLineNumber);
            if ($lineLink !== '')
            {
                $lineLink = htmlspecialchars($lineLink);
                $lineTag = "<a href='{$lineLink}'>#{$currentLineNumber}</a>";
            }
            else
            {
                $lineTag = '#' . $currentLineNumber;
            }
            $currentLine = $lineTag
                . str_pad('',  $maxStrLen - strlen($currentLineNumber), ' ')
                . htmlspecialchars($currentLine);
        }
        $outputLines[$line-1] = '<span class="error-line">' . $outputLines[$line-1] . '</span>';
        return implode("\n", $outputLines);
    }

    private function showFull($var, $dump)
    {
        if (is_scalar($var) || is_null($var))
        {
            if (!is_string($var) || strlen($var) <= 30)
            {
                return true;
            }
        }

        if (is_array($var) && count($var) <= 10)
        {
            return true;
        }

        return false;
    }

    private function getType($var)
    {
        if (is_object($var))
        {
            return '<strong>' . get_class($var) . '</strong>';
        }

        if (is_array($var))
        {
            return 'array(' . count($var) . ')';
        }

        return \gettype($var);
    }
}