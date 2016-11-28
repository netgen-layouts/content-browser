<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\ContentBrowser\Exceptions\RuntimeException;

class BackendRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.registry.backend';
    const TAG_NAME = 'netgen_content_browser.backend';

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

        $backendRegistry = $container->findDefinition(self::SERVICE_NAME);
        $backends = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($backends as $backend => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['item_type'])) {
                    throw new RuntimeException(
                        "Backend definition must have a 'item_type' attribute in its' tag."
                    );
                }

                $backendRegistry->addMethodCall(
                    'addBackend',
                    array($tag['item_type'], new Reference($backend))
                );
            }
        }
    }
}
