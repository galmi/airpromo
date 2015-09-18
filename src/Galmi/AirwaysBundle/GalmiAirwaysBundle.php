<?php

namespace Galmi\AirwaysBundle;

use Galmi\AirwaysBundle\DependencyInjection\Compiler\Source;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GalmiAirwaysBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Source());
    }
}
