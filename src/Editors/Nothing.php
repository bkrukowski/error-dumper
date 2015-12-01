<?php

namespace ErrorDumper\Editors;

class Nothing implements EditorInterface
{
    public function createLinkToFile($file, $line)
    {
        throw new CannotGenerateLinkException();
    }

    public function registerDirectoryMap($serverPath, $projectPath)
    {
        return $this;
    }
}