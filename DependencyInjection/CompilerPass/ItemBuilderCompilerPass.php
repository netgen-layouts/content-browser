<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ItemBuilderCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('netgen_content_browser.item_builder')) {
            return;
        }

        $converters = $container->findTaggedServiceIds('netgen_content_browser.converter');
        $itemBuilder = $container->findDefinition('netgen_content_browser.item_builder');

        foreach ($converters as $serviceName => $tag) {
            $itemBuilder->addMethodCall(
                'addConverter',
                array(
                    $tag[0]['identifier'],
                    new Reference($serviceName),
                )
            );
        }
    }
}
