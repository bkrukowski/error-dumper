<?php

namespace ErrorDumper\Editors;

interface EditorInterface
{
    /**
     * @param $file
     * @param $line
     * @return string
     * @throws CannotGenerateLinkException
     */
    public function createLinkToFile($file, $line);

    /**
     * @param $serverPath
     * @param $projectPath
     * @return EditorInterface
     */
    public function registerDirectoryMap($serverPath, $projectPath);
}