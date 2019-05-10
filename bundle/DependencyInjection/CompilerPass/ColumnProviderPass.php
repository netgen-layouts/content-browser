<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ColumnProviderPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_content_browser.column_provider';
    private const TAG_NAME = 'netgen_content_browser.column_value_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $columnProvider = $container->findDefinition(self::SERVICE_NAME);
        $valueProviderServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $valueProviders = [];
        foreach ($valueProviderServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new RuntimeException(
                        "Column value provider definition must have a 'identifier' attribute in its' tag."
                    );
                }

                $valueProviders[$tag['identifier']] = new ServiceClosureArgument(new Reference($serviceName));
            }
        }

        $columnProvider->addArgument(new Definition(ServiceLocator::class, [$valueProviders]));
    }
}
