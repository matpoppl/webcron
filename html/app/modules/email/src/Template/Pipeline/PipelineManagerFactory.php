<?php

namespace matpoppl\Email\Template\Pipeline;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\Email\Entity\TemplateEntity;

class PipelineManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        /** @var \matpoppl\EntityManager\EntityManager $em */
        $em = $container->get('entity.manager');
        
        $repo = $em->getRepository(TemplateEntity::class);

        return new $name($repo);
    }
}
