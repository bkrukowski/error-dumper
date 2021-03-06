<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\Html;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Editors\Nothing;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Helpers\Stream as StreamHelper;
/**
 * @package ErrorDumper
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestAll
     * @param EditorInterface $editor
     */
    public function testAll(EditorInterface $editor)
    {
        $editorMock = $this->createEditorMock();
        $used = false;
        $editorMock->method('createLinkToFile')
            ->willReturnCallback(function ($file, $line) use (&$used, $editor) {
                $used = true;

                return $editor->createLinkToFile($file, $line);
            });

        $stream = tmpfile();
        $html = new Html($editor, $stream);
        $this->assertSame($html, $html->setBootstrapCss(Html::BOOTSTRAP_CSS));
        $this->assertSame($html, $html->setJqueryJs(Html::JQUERY_JS));
        $this->assertSame($html, $html->setBootstrapJs(Html::BOOTSTRAP_JS));
        $this->assertSame($html, $html->setVarDumpFn(new LightVarDumper()));
        $this->assertSame($html, $html->setEditor($editorMock));
        $html->displayException(new \Exception(__FILE__));
        $streamObj = new StreamHelper($stream);
        $output = $streamObj->getContents();
        $this->assertContains(Html::TAG_HTML, $output);
        $this->assertContains(Html::TAG_UNDER_TITLE, $output);
        $this->assertContains(__FILE__, $output);
        $this->assertTrue($used);
    }

    public function providerTestAll()
    {
        return array(
            array(new PhpStorm()),
            array(new Nothing()),
        );
    }

    /**
     * @return EditorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createEditorMock()
    {
        $className = 'ErrorDumper\Editors\EditorInterface';
        $version = new \PHPUnit_Runner_Version();
        if (version_compare($version->id(), '5.4', '>=')) {
            return $this->createMock($className);
        }

        return $this->getMock($className);
    }
}
