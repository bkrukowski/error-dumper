<?php

namespace ErrorDumper\Console;

class Printer implements PrinterInterface
{
    const COLOR_BLACK = '0';
    const COLOR_RED = '1';
    const COLOR_GREEN = '2';
    const COLOR_YELLOW = '3';
    const COLOR_BLUE = '4';
    const COLOR_MAGENTA = '5';
    const COLOR_CYAN = '6';
    const COLOR_WHITE = '7';

    const STYLE_NORMAL = '0';
    const STYLE_BOLD = '1';
    const STYLE_UNDERLINED = '4';
    const STYLE_BLINKING = '5';
    const STYLE_REVERSE = '7';

    private $stream;

    public function __construct($outputStream)
    {
        $this->setOutputStream($outputStream);
    }

    public function errorBox($text)
    {
        $this->colorizeAndPrint(static::COLOR_RED, null, static::STYLE_BOLD, $text . PHP_EOL);

        return $this;
    }

    public function infoBox($text)
    {
        $this->colorizeAndPrint(static::COLOR_BLUE, null, null, $text . PHP_EOL);

        return $this;
    }

    public function defaultBox($line)
    {
        $this->printRaw($line . PHP_EOL);

        return $this;
    }

    public function setOutputStream($stream)
    {
        $this->stream = $stream;

        return $this;
    }

    public function warningBox($text)
    {
        $this->colorizeAndPrint(static::COLOR_GREEN, null, null, $text . PHP_EOL);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param $textColor
     * @param $backgroundColor
     * @param $style
     * @param $text
     */
    private function colorizeAndPrint($textColor, $backgroundColor, $style, $text)
    {
        $colored = '';

        if (!is_null($textColor)) {
            $colored .= "\033[3" . $textColor . 'm';
        }

        if (!is_null($style)) {
            $colored .= "\033[" . $style . 'm';
        }

        if (!is_null($backgroundColor)) {
            $colored .= "\033[4" . $backgroundColor . 'm';
        }

        $this->printRaw($colored . $text . "\033[0m");
    }

    private function printRaw($text)
    {
        fputs($this->stream, $text);
    }
}
