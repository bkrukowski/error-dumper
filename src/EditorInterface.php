<?php

namespace ErrorDumper;

interface EditorInterface
{
    public function createLinkToFile($file, $line);
}