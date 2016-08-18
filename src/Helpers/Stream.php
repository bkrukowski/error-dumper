<?php

namespace ErrorDumper\Helpers;

/**
 * @internal
 */
class Stream
{
    private $stream;

    final public function __construct($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @deprecated
     * @param $stream
     * @return string
     */
    public static function getContentsFromStream($stream)
    {
        $self = new static($stream);

        return $self->getContents();
    }

    public function getContents()
    {
        rewind($this->stream);
        $result = '';
        while (!feof($this->stream)) {
            $result .= fread($this->stream, 1024);
        }

        return $result;
    }
}
