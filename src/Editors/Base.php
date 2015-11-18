<?php

namespace ErrorDumper\Editors;

use ErrorDumper\EditorInterface;

abstract class Base implements EditorInterface
{
    private $mapping = [];

    public function createLinkToFile($file, $line)
    {
        return $this->getProtocol() . '://open?' . http_build_query([
            'file' => $this->convertPath($file),
            'line' => $line
        ]);
    }

    public function registerDirectoryMap($serverPath, $projectPath)
    {
        $this->mapping[$serverPath] = $projectPath;
    }

    abstract protected function getProtocol();

    private function convertPath($path)
    {
        $result = $path;
        foreach ($this->mapping as $from => $to)
        {
            $pattern = '#^' . preg_quote($from, '#') . '#';
            $result = preg_replace($pattern, $to, $result);
        }

        return $result;
    }
}