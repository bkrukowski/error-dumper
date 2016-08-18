<?php

namespace ErrorDumper\Tests\SyntaxChecker;

class SyntaxCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testSyntax()
    {
        $path = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', 'src')));
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $file) {
            require_once $file[0];
        }
    }
}