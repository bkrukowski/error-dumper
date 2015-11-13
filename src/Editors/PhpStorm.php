<?php

namespace ErrorDumper\Editors;

class PhpStorm extends Base
{
    /**
     * @codeCoverageIgnore
     */
    public function getProtocol()
    {
        return 'idea';
    }
}