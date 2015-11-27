<?php

namespace ErrorDumper\Editors;

class Nothing implements EditorInterface
{
    public function createLinkToFile($file, $line)
    {
        return '';
    }

    public function registerDirectoryMap($serverPath, $projectPath)
    {
        return $this;
    }
}