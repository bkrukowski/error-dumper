<?php

namespace ErrorDumper\Handlers;

abstract class PreCallbackEvent implements CallbackEventInterface
{
    public function stopDisplay()
    {
        throw new StopDisplayException();
    }
}
