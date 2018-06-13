<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ColumnProviderPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_content_browser.column_provider';
    private static $tagName = 'netgen_content_browser.column_value_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $columnProvider = $container->findDefinition(self::$serviceName);
        $valueProviderServices = $container->findTaggedServiceIds(self::$tagName);

        $valueProviders = [];
        foreach ($valueProviderServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new RuntimeException(
                        "Column value provider definition must have a 'identifier' attribute in its' tag."
                    );
                }

                $valueProviders[$tag['identifier']] = new Reference($serviceName);
            }
        }

        $columnProvider->replaceArgument(2, $valueProviders);
    }
}
