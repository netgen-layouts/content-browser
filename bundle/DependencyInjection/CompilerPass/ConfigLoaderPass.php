<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigLoaderPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('netgen_content_browser.config_loader')) {
            return;
        }

        $configLoader = $container->findDefinition('netgen_content_browser.config_loader');
        $configProcessors = $container->findTaggedServiceIds('netgen_content_browser.config_processor');

        uasort(
            $configProcessors,
            function ($a, $b) {
                $priorityA = isset($a[0]['priority']) ? $a[0]['priority'] : 0;
                $priorityB = isset($b[0]['priority']) ? $b[0]['priority'] : 0;

                return $priorityB - $priorityA;
            }
        );

        $configProcessorReferences = array();
        foreach (array_keys($configProcessors) as $configProcessor) {
            $configProcessorReferences[] = new Reference($configProcessor);
        }

        $configLoader->replaceArgument(1, $configProcessorReferences);
    }
}
