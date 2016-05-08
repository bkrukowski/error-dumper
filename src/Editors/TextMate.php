<?php

namespace ErrorDumper\Editors;

class TextMate extends Base
{
    /**
     * @codeCoverageIgnore
     */
    public function getProtocol()
    {
        return 'txmt';
    }
}
