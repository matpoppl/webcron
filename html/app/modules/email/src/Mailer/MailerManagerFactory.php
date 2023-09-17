<?php

namespace matpoppl\Email\Mailer;

use Psr\Container\ContainerInterface;
use matpoppl\ServiceManager\Factory\FactoryInterface;
use matpoppl\Email\Template\Pipeline\PipelineRenderer;

class MailerManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, ...$args)
    {
        $tplManager = $container->get('email.template.manager');
        
        $pipeManager = $container->get('email.template.pipeline.manager');
        
        $renderer = new PipelineRenderer();
        
        return new $name($tplManager, $pipeManager, $renderer);
    }
}
