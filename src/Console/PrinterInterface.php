<?php

namespace ErrorDumper\Console;

interface PrinterInterface
{
    /**
     * @param $stream
     * @return PrinterInterface
     */
    public function setOutputStream($stream);

    /**
     * @param $line
     * @return PrinterInterface
     */
    public function defaultBox($line);

    /**
     * @param $text
     * @return PrinterInterface
     */
    public function errorBox($text);

    /**
     * @param $text
     * @return PrinterInterface
     */
    public function infoBox($text);

    /**
     * @param $text
     * @return PrinterInterface
     */
    public function warningBox($text);
}
