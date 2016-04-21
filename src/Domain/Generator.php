<?php

namespace Atypicalbrands\RepositoryGenerator\Domain;

use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;

class Generator
{
    public function generate()
    {
        $files = new \RecursiveIteratorIterator(new \DirectoryIterator('app'));
        foreach ($files as $file) {
            $metadata = PhpClass::fromFile($file);
            if (!$metadata->getName() || !$metadata->getDocblock()->getTags('Repository')->get(0)) {
                continue;
            }

            $class = (new PhpClass())->setName($metadata->getName() . 'Repository')
                ->setNamespace($metadata->getNamespace())
                ->setUseStatements(
                    [
                        'Doctrine\ORM\EntityRepository',
                        'LaravelDoctrine\ORM\Pagination\Paginatable',
                    ]
                )
                ->setParentClassName('EntityRepository')
                ->setTraits(['Paginatable'])
                ->setMethods(
                    [
                        (new PhpMethod('create'))->setBody('return mew ' . $metadata->getName() . '();'),
                        (new PhpMethod('save'))->setParameters(
                                [(new PhpParameter('entity'))->setType($metadata->getName())]
                            )
                            ->setBody(
                                "\$this->getEntityManager()->persist(\$entity);\n\$this->getEntityManager()->flush();"
                            ),
                        (new PhpMethod('delete'))->setParameters(
                            [(new PhpParameter('entity'))->setType($metadata->getName())]
                        )
                            ->setBody(
                                "\$this->getEntityManager()->persist(\$entity);\n\$this->getEntityManager()->flush();"
                            ),
                    ]
                )
            ;
            
            var_dump((new CodeGenerator())->generate($class));
        }
    }
}
