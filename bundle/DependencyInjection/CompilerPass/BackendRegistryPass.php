<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class BackendRegistryPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_content_browser.registry.backend';
    private static $tagName = 'netgen_content_browser.backend';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $backendRegistry = $container->findDefinition(self::$serviceName);
        $backends = $container->findTaggedServiceIds(self::$tagName);

        foreach ($backends as $backend => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['item_type'])) {
                    throw new RuntimeException(
                        "Backend definition must have a 'item_type' attribute in its' tag."
                    );
                }

                $backendRegistry->addMethodCall(
                    'addBackend',
                    [$tag['item_type'], new Reference($backend)]
                );
            }
        }
    }
}
