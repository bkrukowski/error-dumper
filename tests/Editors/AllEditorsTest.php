<?php

namespace ErrorDumper\Tests\Editors;

use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Editors\MacVim;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Editors\TextMate;
use ErrorDumper\Tests\TestBase;

class AllEditorsTest extends TestBase
{
    /**
     * @dataProvider provider_test_createLinkToFile
     * @param EditorInterface $editor
     * @param $file
     * @param $line
     */
    public function test_createLinkToFile(EditorInterface $editor, $file, $line)
    {
        $link = $editor->createLinkToFile($file, $line);
        $this->assertTrue(is_string($link));
        $this->assertContains((string) $line, $link);
    }

    public function provider_test_createLinkToFile()
    {
        return $this->prepareDataProvider(array(
            array(new MacVim(), __FILE__, __LINE__),
            array(new PhpStorm(), __FILE__, __LINE__),
            array(new TextMate(), __FILE__, __LINE__),
            array(new PhpStorm(), '/a/b/c', 456),
        ));
    }

    /**
     * @dataProvider provider_test_registerDirectoryMap
     * @param EditorInterface $editor
     * @param $file
     * @param $line
     * @param $expectedResultFile
     */
    public function test_registerDirectoryMap(EditorInterface $editor, $from, $to, $file, $line, $expectedResultFile)
    {
        $editor->registerDirectoryMap($from, $to);
        $link = $editor->createLinkToFile($file, $line);
        list(, $stringParams) = explode('?', $link, 2);
        parse_str($stringParams, $data);
        $this->assertEquals($line, $data['line']);
        $this->assertSame($expectedResultFile, $data['file']);
    }

    public function provider_test_registerDirectoryMap()
    {
        $from = '/var/www';
        $to = '~/projects/error-dumper';
        $suffix = '/foo/bar/foobar.php';

        $result = array();
        foreach (array('PhpStorm', 'MacVim', 'TextMate') as $class)
        {
            $class = 'ErrorDumper\Editors\\' . $class;
            /** @var EditorInterface $editor */
            $editor = new $class();
            $result[] = array($editor, $from, $to, $from . $suffix, __LINE__, $to . $suffix);
        }

        return $this->prepareDataProvider($result);
    }
}