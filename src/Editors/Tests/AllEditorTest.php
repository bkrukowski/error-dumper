<?php

namespace ErrorDumper\Editors\Tests;

use ErrorDumper\EditorInterface;
use ErrorDumper\Editors\MacVim;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Editors\TextMate;

class AllEditorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider editorsProvider
     * @param EditorInterface $editor
     * @param $file
     * @param $line
     */
    public function testEditor(EditorInterface $editor, $file, $line)
    {
        $link = $editor->createLinkToFile($file, $line);
        $this->assertTrue(is_string($link));
        $this->assertContains((string) $line, $link);
    }

    public function editorsProvider()
    {
        return [
            [new MacVim(), __FILE__, __LINE__],
            [new PhpStorm(), __FILE__, __LINE__],
            [new TextMate(), __FILE__, __LINE__],
            [new PhpStorm(), '/a/b/c', 456],
        ];
    }
}