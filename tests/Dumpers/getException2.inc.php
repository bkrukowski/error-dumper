<?php

if (!function_exists('bkrukowskiMyStrangeFunction')) {
    function bkrukowskiMyStrangeFunction(array $param1, stdClass $param2)
    {
        return new \Exception();
    }
}

return bkrukowskiMyStrangeFunction(array(1, 2, 3), new stdClass());
