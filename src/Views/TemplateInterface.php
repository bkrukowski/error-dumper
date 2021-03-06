<?php

namespace ErrorDumper\Views;

/**
 * @internal
 */
interface TemplateInterface
{
    /**
     * @param string $path
     * @param array $data
     * @return string
     */
    public function render($path, array $data);

    public function setRootDirectory($dir);
}
