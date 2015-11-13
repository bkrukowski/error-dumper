<?php

namespace ErrorDumper;

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
     * @param \Exception|\Throwable $e
     */
    public function __invoke($e);

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