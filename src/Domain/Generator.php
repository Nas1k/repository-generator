<?php

namespace Atypicalbrands\RepositoryGenerator\Domain;

use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\docblock\Docblock;
use gossi\docblock\tags\ReturnTag;
use gossi\docblock\tags\UnknownTag;

class Generator
{
    public function generate($source, $target)
    {
        $files = new \RecursiveDirectoryIterator($source);
        $serviceProvider = (new PhpClass())->setName('RepositoryServiceProvider')
            ->setNamespace('Atypicalbrands\RepositoryGenerator\Providers')
            ->addUseStatement('Doctrine\ORM\Mapping\ClassMetadata')
            ->addUseStatement('Illuminate\Support\ServiceProvider')
            ->setParentClassName('ServiceProvider');
        $resisterRepository = '';

        foreach (new \RecursiveIteratorIterator($files) as $file) {
            $metadata = PhpClass::fromFile($file);
            if (!$metadata->getName() || !$metadata->getDocblock()->getTags('ORM\Entity')->get(0)) {
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
                ->setDocblock((new Docblock())->appendTag(new UnknownTag('ORM\Entity')))
                ->setParentClassName('EntityRepository')
                ->setTraits(['Paginatable'])
                ->setMethods(
                    [
                        (new PhpMethod('create'))->setDocblock(
                            (new Docblock())->appendTag((new ReturnTag($metadata->getName())))
                                ->setShortDescription('Create new entity instance')
                        )
                            ->setBody('return new ' . $metadata->getName() . '();'),
                        (new PhpMethod('save'))->setDocblock(
                            (new Docblock())->setShortDescription('Save entity')
                        )
                            ->setParameters(
                                [(new PhpParameter('entity'))->setType($metadata->getName())]
                            )
                            ->setBody(
                                "\$this->getEntityManager()->persist(\$entity);\n\$this->getEntityManager()->flush();"
                            ),
                        (new PhpMethod('delete'))->setDocblock(
                            (new Docblock())->setShortDescription('Remove entity')
                        )
                            ->setParameters(
                                [(new PhpParameter('entity'))->setType($metadata->getName())]
                            )
                            ->setBody(
                                "\$this->getEntityManager()->persist(\$entity);\n\$this->getEntityManager()->flush();"
                            ),
                    ]
                )
            ;

            $resisterRepository .= '
                $this->app->bind(\\' . $class->getQualifiedName() . '::class, function ($app) {
                    return new \\' . $class->getQualifiedName()
                . '($app[\'em\'], new ClassMetadata(\\' . $metadata->getQualifiedName() . '::class));                
                });            
            ';

            $filePath = str_replace('\\', '/', $target . '/' . $metadata->getQualifiedName() . 'Repository.php');
            $dir = str_replace('\\', '/', $target . '/' . $metadata->getNamespace());
            file_exists($dir) ?: mkdir($dir, 0777, true);
            file_put_contents($filePath, (new CodeFileGenerator())->generate($class));
        }
        $serviceProvider->setMethods(
            [
                (new PhpMethod('register'))->setBody($resisterRepository),
            ]
        );
        $filePath = str_replace('\\', '/', $target . '/' . $serviceProvider->getQualifiedName() . '.php');
        $dir = str_replace('\\', '/', $target . '/' . $serviceProvider->getNamespace());
        file_exists($dir) ?: mkdir($dir, 0777, true);
        file_put_contents($filePath, (new CodeFileGenerator())->generate($serviceProvider));
    }
}
