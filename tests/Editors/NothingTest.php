<?php

namespace ErrorDumper\Tests\Editors;

use ErrorDumper\Editors\Nothing;

class NothingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \ErrorDumper\Editors\CannotGenerateLinkException
     */
    public function test_createLinkToFile()
    {
        $editor = new Nothing();
        $this->assertSame('', $editor->createLinkToFile(__FILE__, __LINE__));
    }

    public function test_registerDirectoryMap()
    {
        $editor = new Nothing();
        $this->assertSame($editor, $editor->registerDirectoryMap('/var/www', '~/foo/bar'));
    }
}