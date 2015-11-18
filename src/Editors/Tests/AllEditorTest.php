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

    /**
     * @dataProvider mappingProvider
     * @param EditorInterface $editor
     * @param $file
     * @param $line
     * @param $expectedResultFile
     */
    public function testMapping(EditorInterface $editor, $file, $line, $expectedResultFile)
    {
        $link = $editor->createLinkToFile($file, $line);
        list(, $stringParams) = explode('?', $link, 2);
        parse_str($stringParams, $data);
        $this->assertEquals($line, $data['line']);
        $this->assertSame($expectedResultFile, $data['file']);
    }

    public function mappingProvider()
    {
        $from = '/var/www';
        $to = '~/projects/error-dumper';
        $suffix = '/foo/bar/foobar.php';

        foreach ([PhpStorm::class, MacVim::class, TextMate::class] as $class)
        {
            /** @var EditorInterface $editor */
            $editor = new $class();
            $editor->registerDirectoryMap($from, $to);
            yield [$editor, $from . $suffix, __LINE__, $to . $suffix];
        }
    }
}