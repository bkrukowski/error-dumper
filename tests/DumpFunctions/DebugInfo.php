<?php
namespace ErrorDumper\Tests\DumpFunctions;

/**
 * @internal
 */
class DebugInfo
{
    public function __debugInfo()
    {
        return array(
            'key' => 'value',
        );
    }
}