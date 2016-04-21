<?php

namespace Atypicalbrands\RepositoryGenerator\Test\Console\Commands;

use Atypicalbrands\RepositoryGenerator\Console\Commands\Generate;

class GenerateTest extends \PHPUnit_Framework_TestCase
{
    public function handleTest()
    {
        $generator = $this->getMockBuilder('Atypicalbrands\RepositoryGenerator\Domain\Generator')
            ->disableOriginalConstructor()
            ->getMock();

        $generator->expects($this->once())
            ->method('generate');
        /** @var $generator \Atypicalbrands\RepositoryGenerator\Domain\Generator */

        (new Generate($generator))->handle();
    }
}
