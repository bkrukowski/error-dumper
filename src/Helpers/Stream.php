<?php

namespace ErrorDumper\Helpers;

/**
 * @internal
 */
class Stream
{
    public static function getContentsFromStream($stream)
    {
        rewind($stream);
        $result = '';
        while (!feof($stream)) {
            $result .= fread($stream, 1024);
        }

        return $result;
    }
}
