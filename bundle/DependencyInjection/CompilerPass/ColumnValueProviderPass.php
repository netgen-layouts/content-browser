<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ColumnValueProviderPass implements CompilerPassInterface
{
    private const string SERVICE_NAME = 'netgen_content_browser.column_provider';
    private const string TAG_NAME = 'netgen_content_browser.column_value_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $columnProvider = $container->findDefinition(self::SERVICE_NAME);
        $valueProviders = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['identifier'])) {
                    $valueProviders[$tag['identifier']] = new ServiceClosureArgument(new Reference($serviceName));
                }
            }
        }

        $columnProvider->addArgument(new Definition(ServiceLocator::class, [$valueProviders]));
    }
}
