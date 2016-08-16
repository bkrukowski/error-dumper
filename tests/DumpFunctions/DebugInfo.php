<?php
namespace ErrorDumper\TestsDumpFunctions;

/**
 * @internal
 */
class DebugInfo
{
    public function __debugInfo()
    {
        return [
            'key' => 'value',
        ];
    }
}