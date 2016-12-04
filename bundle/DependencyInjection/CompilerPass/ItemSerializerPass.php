<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ItemSerializerPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.item_serializer';
    const TAG_NAME = 'netgen_content_browser.serializer.handler';

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

        $serializer = $container->findDefinition(self::SERVICE_NAME);
        $handlerServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $handlers = array();
        foreach ($handlerServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['item_type'])) {
                    throw new RuntimeException(
                        "Item serializer handler definition must have a 'item_type' attribute in its' tag."
                    );
                }

                $handlers[$tag['item_type']] = new Reference($serviceName);
            }
        }

        $serializer->replaceArgument(4, $handlers);
    }
}
