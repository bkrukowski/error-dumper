<?php

namespace ErrorDumper;

interface EditorInterface
{
    public function createLinkToFile($file, $line);

    /**
     * @param $serverPath
     * @param $projectPath
     * @return EditorInterface
     */
    public function registerDirectoryMap($serverPath, $projectPath);
}