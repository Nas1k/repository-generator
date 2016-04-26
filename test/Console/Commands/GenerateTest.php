<?php

namespace Atypicalbrandsllc\RepositoryGenerator\Test\Console\Commands;

use Atypicalbrandsllc\RepositoryGenerator\Console\Commands\Generate;

class GenerateTest extends \PHPUnit_Framework_TestCase
{
    public function handleTest()
    {
        $generator = $this->getMockBuilder('Atypicalbrands\RepositoryGenerator\Domain\Generator')
            ->disableOriginalConstructor()
            ->getMock();

        $generator->expects($this->once())
            ->method('generate');
        /** @var $generator \Atypicalbrandsllc\RepositoryGenerator\Domain\Generator */

        (new Generate($generator))->handle();
    }
}
