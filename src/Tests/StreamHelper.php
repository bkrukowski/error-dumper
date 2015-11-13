<?php

namespace ErrorDumper\Tests;

class StreamHelper
{
    public static function getContentsFromStream($stream)
    {
        rewind($stream);
        $result = '';
        while (!feof($stream))
        {
            $result .= fread($stream, 1024);
        }

        return $result;
    }
}