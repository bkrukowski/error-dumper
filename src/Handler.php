<?php

namespace ErrorDumper;

use ErrorDumper\Helpers\Exceptions;

class Handler implements HandlerInterface
{
    /**
     * @var DumperInterface
     */
    private $dumper;

    /**
     * @var callable
     */
    private $postCallback;

    /**
     * @var callable
     */
    private $preCallback;

    public function __construct(DumperInterface $dumper)
    {
        $this->setDumper($dumper);
    }

    public function setDumper(DumperInterface $dumper)
    {
        $this->dumper = $dumper;

        return $this;
    }

    public function getDumper()
    {
        return $this->dumper;
    }

    public function setPostCallback($callback)
    {
        Exceptions::throwIfIsNotCallable($callback);
        $this->postCallback = $callback;

        return $this;
    }

    public function setPreCallback($callback)
    {
        Exceptions::throwIfIsNotCallable($callback);
        $this->preCallback = $callback;

        return $this;
    }

    /**
     * @param \Exception|\Throwable $e
     */
    public function __invoke($e)
    {
        Exceptions::throwIfIsNotThrowable($e);
        if (!empty($fn = $this->preCallback))
        {
            try
            {
                $fn($e);
            }
            catch (StopDisplayException $stopE)
            {
                return;
            }
        }
        $this->dumper->displayException($e);
        if (!empty($fn = $this->postCallback))
        {
            $fn($e);
        }
    }
}