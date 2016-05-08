<?php

namespace ErrorDumper\Views;

/**
 * @internal
 */
class Template implements TemplateInterface
{
    private $rootDirectory;

    public function __construct()
    {
        $this->setRootDirectory(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', 'templates')));
    }

    public function render($path, array $data = array())
    {
        // @codeCoverageIgnoreStart
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        // @codeCoverageIgnoreStop
        $realPath = implode(DIRECTORY_SEPARATOR, array($this->rootDirectory, $path . '.php'));
        ob_start();
        require $realPath;
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public function setRootDirectory($dir)
    {
        $this->rootDirectory = $dir;
    }
}
