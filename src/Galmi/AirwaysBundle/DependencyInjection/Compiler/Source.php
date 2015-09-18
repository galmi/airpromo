<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 31.07.15
 * Time: 12:48
 */

namespace Galmi\AirwaysBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Source implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('galmi_airways.searcher')) {
            return;
        }

        $definition = $container->findDefinition(
            'galmi_airways.searcher'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'galmi_airways.source'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addSource',
                array(new Reference($id))
            );
        }
    }
}