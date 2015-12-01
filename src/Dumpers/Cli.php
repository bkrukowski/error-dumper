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

    /**
     * @var callable
     */
    private $windowWidthGetter;

    public function __construct($stream)
    {
        $this->printer = new Printer($stream);
        $this->setOutputStream($stream);
    }

    public function displayException($e)
    {
        Exceptions::throwIfIsNotThrowable($e);
        $width = $this->getWindowWidth();
        $this->printer->defaultBox('');
        $this->printer->defaultBox('');
        $this->printer->errorBox(str_pad('', $width, '#'));
        $this->printer->errorBox(get_class($e) . ': ' . $e->getMessage());
        $this->printer->errorBox(str_pad('', $width, '#'));
        $this->printer->defaultBox('');
        $this->printer->defaultBox('');
        $this->printer->defaultBox($e->getTraceAsString());
    }

    public function setOutputStream($output)
    {
        parent::setOutputStream($output);
        $this->printer->setOutputStream($output);

        return $this;
    }

    /**
     * @param callable $getter
     * @return Cli
     */
    public function setWindowWidthGetter($getter)
    {
        Exceptions::throwIfIsNotCallable($getter);
        $this->windowWidthGetter = $getter;

        return $this;
    }

    private function getWindowWidth()
    {
        return $this->windowWidthGetter
            ? call_user_func($this->windowWidthGetter)
            : static::EMPHASIS_LENGTH;
    }
}