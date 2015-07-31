<?php

namespace Galmi\AirwaysBundle;

use Galmi\AirwaysBundle\Handlers\ParserCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GalmiAirwaysBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ParserCompilerPass());
    }
}
