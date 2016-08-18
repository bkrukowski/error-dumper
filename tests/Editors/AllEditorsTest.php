<?php

namespace ErrorDumper\Tests\Editors;

use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Editors\MacVim;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Editors\TextMate;

class AllEditorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestCreateLinkToFile
     * @param EditorInterface $editor
     * @param $file
     * @param $line
     */
    public function testCreateLinkToFile(EditorInterface $editor, $file, $line)
    {
        $link = $editor->createLinkToFile($file, $line);
        $this->assertTrue(is_string($link));
        $this->assertContains((string) $line, $link);
    }

    public function providerTestCreateLinkToFile()
    {
        return array(
            array(new MacVim(), __FILE__, __LINE__),
            array(new PhpStorm(), __FILE__, __LINE__),
            array(new TextMate(), __FILE__, __LINE__),
            array(new PhpStorm(), '/a/b/c', 456),
        );
    }

    /**
     * @dataProvider providerTestRegisterDirectoryMap
     * @param EditorInterface $editor
     * @param $fromDir
     * @param $toDir
     * @param $file
     * @param $line
     * @param $expectedResultFile
     */
    public function testRegisterDirectoryMap(
        EditorInterface $editor,
        $fromDir,
        $toDir,
        $file,
        $line,
        $expectedResultFile
    ) {
        $editor->registerDirectoryMap($fromDir, $toDir);
        $link = $editor->createLinkToFile($file, $line);
        list(, $stringParams) = explode('?', $link, 2);
        parse_str($stringParams, $data);
        $this->assertEquals($line, $data['line']);
        $this->assertSame($expectedResultFile, $data['file']);
    }

    public function providerTestRegisterDirectoryMap()
    {
        $fromDir = '/var/www';
        $toDir = '~/projects/error-dumper';
        $suffix = '/foo/bar/foobar.php';

        $result = array();
        foreach (array('PhpStorm', 'MacVim', 'TextMate') as $class) {
            $class = 'ErrorDumper\Editors\\' . $class;
            /** @var EditorInterface $editor */
            $editor = new $class();
            $result[] = array($editor, $fromDir, $toDir, $fromDir . $suffix, __LINE__, $toDir . $suffix);
        }

        return $result;
    }
}
