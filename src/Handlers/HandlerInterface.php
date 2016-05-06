<?php

namespace ErrorDumper\Handlers;

use ErrorDumper\Dumpers\DumperInterface;

interface HandlerInterface
{
    /**
     * @param DumperInterface $dumper
     * @return HandlerInterface
     */
    public function setDumper(DumperInterface $dumper);

    /**
     * @return DumperInterface
     */
    public function getDumper();

    /**
     * @param \Exception|\Throwable $exception
     */
    public function __invoke($exception);

    /**
     * @param callable $callback
     * @return HandlerInterface
     */
    public function setPreCallback($callback);

    /**
     * @param callable $callback
     * @return HandlerInterface
     */
    public function setPostCallback($callback);
}