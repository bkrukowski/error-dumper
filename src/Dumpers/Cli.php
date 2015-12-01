<?php

namespace ErrorDumper\Dumpers;

use ErrorDumper\Console\Printer;
use ErrorDumper\Console\PrinterInterface;
use ErrorDumper\Helpers\Exceptions;

class Cli extends Base
{
    const EMPHASIS_LENGTH = 50;

    /**
     * @var PrinterInterface
     */
    private $printer;

    public function __construct($stream)
    {
        $this->printer = new Printer($stream);
        $this->setOutputStream($stream);
    }

    public function displayException($e)
    {
        Exceptions::throwIfIsNotThrowable($e);
        $this->printer->defaultBox('');
        $this->printer->errorBox(str_pad('', static::EMPHASIS_LENGTH, '#'));
        $this->printer->errorBox(get_class($e) . ': ' . $e->getMessage());
        $this->printer->errorBox(str_pad('', static::EMPHASIS_LENGTH, '#'));
        $this->printer->defaultBox('');
        $this->printer->defaultBox($e->getTraceAsString());
    }

    public function setOutputStream($output)
    {
        parent::setOutputStream($output);
        $this->printer->setOutputStream($output);

        return $this;
    }
}