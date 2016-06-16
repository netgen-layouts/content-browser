<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class ItemRendererPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.item_renderer';
    const TAG_NAME = 'netgen_content_browser.template_value_provider';

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

        $itemRenderer = $container->findDefinition(self::SERVICE_NAME);
        $valueProviderServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $valueProviders = array();
        foreach ($valueProviderServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Template value provider definition must have a 'value_type' attribute in its' tag."
                    );
                }

                $valueProviders[$tag['value_type']] = new Reference($serviceName);
            }
        }

        $itemRenderer->replaceArgument(1, $valueProviders);
    }
}
