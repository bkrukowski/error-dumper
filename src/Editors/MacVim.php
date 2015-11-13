<?php

namespace ErrorDumper\Editors;

class MacVim extends Base
{
    /**
     * @codeCoverageIgnore
     */
    public function getProtocol()
    {
        return 'mvim';
    }
}