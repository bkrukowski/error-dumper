<?php

namespace ErrorDumper\Handlers;

use ErrorDumper\Dumpers\DumperInterface;
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
     * @param \Exception|\Throwable $exception
     */
    public function __invoke($exception)
    {
        $exceptions = new Exceptions();
        $exceptions->throwIfIsNotThrowable($exception);
        $pre = $this->preCallback;
        if (!empty($pre)) {
            try {
                call_user_func($pre, $exception);
            } catch (StopDisplayException $stopE) {
                return;
            }
        }
        $this->dumper->displayException($exception);
        $post = $this->postCallback;
        if (!empty($post)) {
            call_user_func($post, $exception);
        }
    }
}
