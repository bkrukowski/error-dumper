<?php

namespace ErrorDumper;

/**
 * @codeCoverageIgnore
 */
class Autoload
{
    private $namespaces;

    private function __construct()
    {
        $this->namespaces = array(
            array('ErrorDumper', __DIR__),
        );
        spl_autoload_register($this);
    }

    public function __invoke($className)
    {
        foreach ($this->namespaces as $data) {
            list($namespace, $dir) = $data;
            if (strpos($className, $namespace . '\\') === 0) {
                $filename = $dir . substr($className, strlen($namespace)) . '.php';
                $filename = str_replace('\\', DIRECTORY_SEPARATOR, $filename);

                if (is_file($filename)) {
                    require_once $filename;

                    return true;
                }
            }
        }

        return false;
    }

    public static function init()
    {
        static $inited = false;
        if (!$inited) {
            new static();
            $inited = true;
        }
    }
}
