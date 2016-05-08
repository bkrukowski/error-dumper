<?php

namespace ErrorDumper\Tests\Views;

use ErrorDumper\Views\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $template = new Template();
        $template->setRootDirectory(__DIR__);
        $output = $template->render('template', array(
            'value' => 5 * 5,
        ));
        $this->assertSame('5 * 5 = 25', $output);
    }
}
