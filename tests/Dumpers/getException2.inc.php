<?php

if (!function_exists('bkrukowski_myStrangeFunction'))
{
    function bkrukowski_myStrangeFunction(array $param1, stdClass $param2)
    {
        return new \Exception();
    }
}

return bkrukowski_myStrangeFunction(array(1,2,3), new stdClass());