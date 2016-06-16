<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class ValueLoaderRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.registry.value_loader';
    const TAG_NAME = 'netgen_content_browser.value_loader';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $valueLoaderRegistry = $container->findDefinition(self::SERVICE_NAME);
        $valueLoaders = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($valueLoaders as $valueLoader => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Value loader definition must have a 'value_type' attribute in its' tag."
                    );
                }

                $valueLoaderRegistry->addMethodCall(
                    'addValueLoader',
                    array($tag['value_type'], new Reference($valueLoader))
                );
            }
        }
    }
}
