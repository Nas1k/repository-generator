<?php

namespace Atypicalbrands\RepositoryGenerator\Console\Commands;

use Atypicalbrands\RepositoryGenerator\Domain\Generator;
use Illuminate\Console\Command;

class Generate extends Command
{
    protected $signature = 'doctrine:generate:repositories {source} {target}';

    protected $description = 'Generate repositories for entity with tag @Orm\Entity(repository=ClassName)';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
        parent::__construct();
    }

    public function handle()
    {
        $this->generator->generate($this->argument('source'), $this->argument('target'));
    }
}
