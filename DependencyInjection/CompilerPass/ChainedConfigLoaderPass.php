<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ChainedConfigLoaderPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('netgen_content_browser.config_loader.chained')) {
            return;
        }

        $chainedConfigLoader = $container->findDefinition('netgen_content_browser.config_loader.chained');
        $configLoaders = $container->findTaggedServiceIds('netgen_content_browser.config_loader');

        uasort(
            $configLoaders,
            function ($a, $b) {
                $priorityA = isset($a[0]['priority']) ? $a[0]['priority'] : 0;
                $priorityB = isset($b[0]['priority']) ? $b[0]['priority'] : 0;

                if ($priorityA == $priorityB) {
                    return 0;
                }

                return ($priorityA > $priorityB) ? -1 : 1;
            }
        );

        foreach ($configLoaders as $serviceName => $tag) {
            $chainedConfigLoader->addMethodCall(
                'addConfigLoader',
                array(
                    new Reference($serviceName),
                )
            );
        }
    }
}
