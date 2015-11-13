<?php

namespace ErrorDumper;

abstract class PreCallbackEvent implements CallbackEventInterface
{
    public function stopDisplay()
    {
        throw new StopDisplayException();
    }
}