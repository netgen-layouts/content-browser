<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class BackendPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_content_browser.registry.backend';
    private const TAG_NAME = 'netgen_content_browser.backend';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $backendRegistry = $container->findDefinition(self::SERVICE_NAME);
        $backendServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $backends = [];

        foreach ($backendServices as $backend => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['item_type'])) {
                    throw new RuntimeException(
                        "Backend definition must have a 'item_type' attribute in its' tag."
                    );
                }

                $backends[$tag['item_type']] = new Reference($backend);
            }
        }

        $backendRegistry->replaceArgument(0, $backends);
    }
}
