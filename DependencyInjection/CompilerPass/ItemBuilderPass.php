<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ItemBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.item_builder';
    const TAG_NAME = 'netgen_content_browser.converter';

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

        $itemBuilder = $container->findDefinition(self::SERVICE_NAME);
        $itemConverterServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $itemConverters = array();
        foreach ($itemConverterServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                $itemConverters[$tag['value_type']] = new Reference($serviceName);
            }
        }

        $itemBuilder->replaceArgument(2, $itemConverters);
    }
}
