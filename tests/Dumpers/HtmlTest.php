<?php

namespace ErrorDumper\Tests\Dumpers;

use ErrorDumper\Dumpers\Html;
use ErrorDumper\DumpFunctions\InternalVarDumper;
use ErrorDumper\Editors\EditorInterface;
use ErrorDumper\Editors\PhpStorm;
use ErrorDumper\Tests\StreamHelper;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        /** @var EditorInterface|\PHPUnit_Framework_MockObject_MockObject $editorMock */
        $editorMock = $this->getMock('ErrorDumper\Editors\EditorInterface');
        $used = false;
        $editorMock->method('createLinkToFile')
            ->willReturnCallback(function () use (&$used) {
                $used = true;
            });

        $stream = tmpfile();
        $html = new Html(new PhpStorm(), $stream);
        $this->assertSame($html, $html->setBootstrapCss(Html::BOOTSTRAP_CSS));
        $this->assertSame($html, $html->setJqueryJs(Html::JQUERY_JS));
        $this->assertSame($html, $html->setBootstrapJs(Html::BOOTSTRAP_JS));
        $this->assertSame($html, $html->setVarDumpFn(new InternalVarDumper()));
        $this->assertSame($html, $html->setEditor($editorMock));
        $html->displayException(new \Exception(__FILE__));
        $output = StreamHelper::getContentsFromStream($stream);
        $this->assertContains(Html::TAG_HTML, $output);
        $this->assertContains(__FILE__, $output);
        $this->assertTrue($used);
    }
}