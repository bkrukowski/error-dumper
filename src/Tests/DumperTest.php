<?php

namespace ErrorDumper\Tests;

use ErrorDumper\Dumper;
use ErrorDumper\EditorInterface;
use ErrorDumper\Editors\PhpStorm;

class DumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dumpersProvider
     * @param Dumper $dumper
     * @param \Exception $e
     * @param array $shouldContains
     */
    public function testOutput(Dumper $dumper, \Exception $e, array $shouldContains)
    {
        $stream = tmpfile();
        $dumper->setOutputStream($stream);
        $dumper->displayException($e);
        $output = StreamHelper::getContentsFromStream($stream);
        fclose($stream);
        foreach ($shouldContains as $part)
        {
            $this->assertContains($part, $output);
        }
    }

    /**
     * @expectedException \ErrorDumper\DumperException
     */
    public function testInvalidMode()
    {
        (new Dumper())->setMode('invalid');
    }

    public function dumpersProvider()
    {
        return array_merge($this->htmlDumperDataProvider(), $this->cliDumperDataProvider());
    }

    public function testChain()
    {
        $dumper = new Dumper();
        $this->assertSame($dumper, $dumper->setMode(Dumper::MODE_CLI));
        $this->assertSame($dumper, $dumper->setMode(Dumper::MODE_HTML));
        $this->assertSame($dumper, $dumper->setOutputStream(tmpfile()));
        $this->assertSame($dumper, $dumper->setEditor(new PhpStorm()));
        $this->assertSame($dumper, $dumper->setBootstrapCss('foo'));
        $this->assertSame($dumper, $dumper->setBootstrapJs('bar'));
        $this->assertSame($dumper, $dumper->setJqueryJs('foobar'));
    }

    private function htmlDumperDataProvider()
    {
        $htmlDumper = new Dumper();
        /** @var EditorInterface|\PHPUnit_Framework_MockObject_MockObject $editor */
        $editor = $this->getMockBuilder(EditorInterface::class)->getMock();
        $editor->method('createLinkToFile')->willReturnCallback(function ($file, $line) {
            return md5($file);
        });
        $htmlDumper->setEditor($editor);
        $htmlDumper
            ->setJqueryJs(Dumper::JQUERY_JS)
            ->setBootstrapJs(Dumper::BOOTSTRAP_JS)
            ->setBootstrapCss(Dumper::BOOTSTRAP_CSS);
        $messageHtml = 'My html exception message ' . md5(mt_rand(0, PHP_INT_MAX));

        $htmlSet = [
            $htmlDumper,
            new \Exception($messageHtml),
            [
                Dumper::TAG_HTML,
                Dumper::JQUERY_JS,
                Dumper::BOOTSTRAP_CSS,
                Dumper::BOOTSTRAP_JS,
                md5(__FILE__),
                $messageHtml,
            ]
        ];

        return [
            'html' => $htmlSet,
        ];
    }

    private function cliDumperDataProvider()
    {
        $cliDumper = new Dumper();
        $cliDumper->setMode(Dumper::MODE_CLI);
        $messageCli = 'My cli exception message ' . md5(mt_rand(0, PHP_INT_MAX));
        $cliSet = [
            $cliDumper,
            new \Exception($messageCli),
            [
                $messageCli,
            ]
        ];

        return [
            'cli' => $cliSet,
        ];
    }
}