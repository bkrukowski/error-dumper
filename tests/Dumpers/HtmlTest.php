<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\Html;
use ErrorDumper\DumpFunctions\LightVarDumper;
use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Editors\Nothing;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Helpers\Stream as StreamHelper;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_testAll
     * @param EditorInterface $editor
     */
    public function testAll(EditorInterface $editor)
    {
        /** @var EditorInterface|\PHPUnit_Framework_MockObject_MockObject $editorMock */
        $editorMock = $this->getMock('ErrorDumper\Editors\EditorInterface');
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
        $output = StreamHelper::getContentsFromStream($stream);
        $this->assertContains(Html::TAG_HTML, $output);
        $this->assertContains(Html::TAG_UNDER_TITLE, $output);
        $this->assertContains(__FILE__, $output);
        $this->assertTrue($used);
    }

    public function provider_testAll()
    {
        return array(
            array(new PhpStorm()),
            array(new Nothing()),
        );
    }
}