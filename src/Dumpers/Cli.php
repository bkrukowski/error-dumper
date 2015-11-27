<?php

namespace ErrorDumper\Dumpers;

use ErrorDumper\Views\Template;

class Cli extends Base
{
    public function __construct($stream)
    {
        $this->setOutputStream($stream);
    }

    public function displayException($e)
    {
        $data = array(
            'exception' => $e,
        );
        $template = new Template();
        fputs($this->outputStream, $template->render('exceptions/cli', $data));
    }
}