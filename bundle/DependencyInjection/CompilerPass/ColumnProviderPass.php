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
use function sprintf;

final class ColumnProviderPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

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
            $serviceClass = $this->getDefinitionClass($container, $serviceName);
            $registeredFromTag = false;
            $registeredDefault = false;

            foreach ($tags as $tag) {
                if (isset($tag['identifier'])) {
                    $valueProviders[$tag['identifier']] = new ServiceClosureArgument(new Reference($serviceName));
                    $registeredFromTag = true;

                    continue;
                }

                if (isset($serviceClass::$defaultIdentifier) && !$registeredDefault) {
                    $valueProviders[$serviceClass::$defaultIdentifier] = new ServiceClosureArgument(new Reference($serviceName));
                    $registeredDefault = true;
                }
            }

            if (!$registeredFromTag && !$registeredDefault) {
                throw new RuntimeException(
                    sprintf(
                        'Could not register column value provider "%s". Make sure that either "%s" attribute exists in the tag or a "%s" static property exists in the class.',
                        $serviceName,
                        'identifier',
                        '$defaultIdentifier'
                    )
                );
            }
        }

        $columnProvider->addArgument(new Definition(ServiceLocator::class, [$valueProviders]));
    }
}
