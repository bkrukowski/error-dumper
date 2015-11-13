<?php

namespace ErrorDumper\Editors;

use ErrorDumper\EditorInterface;

abstract class Base implements EditorInterface
{
    public function createLinkToFile($file, $line)
    {
        return $this->getProtocol() . '://open?' . http_build_query([
            'file' => $file,
            'line' => $line
        ]);
    }

    abstract public function getProtocol();
}