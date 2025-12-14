<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function is_string;
use function preg_match;
use function sprintf;

final class ItemTypePass implements CompilerPassInterface
{
    private const string BACKEND_REGISTRY_SERVICE = 'netgen_content_browser.registry.backend';

    private const string CONFIG_REGISTRY_SERVICE = 'netgen_content_browser.registry.config';

    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->has(self::BACKEND_REGISTRY_SERVICE)
            || !$container->has(self::CONFIG_REGISTRY_SERVICE)
        ) {
            return;
        }

        $backendRegistry = $container->findDefinition(self::BACKEND_REGISTRY_SERVICE);
        $configRegistry = $container->findDefinition(self::CONFIG_REGISTRY_SERVICE);

        $backendServices = $container->findTaggedServiceIds('netgen_content_browser.backend');

        $backends = [];
        $configs = [];

        /** @var array<string, mixed[]> $itemTypesConfig */
        $itemTypesConfig = $container->getParameter('netgen_content_browser.item_types');

        foreach ($itemTypesConfig as $itemType => $itemTypeConfig) {
            if (preg_match('/^[A-Za-z]\w*$/', $itemType) !== 1) {
                throw new RuntimeException(
                    sprintf(
                        'Item type must begin with a letter and be followed by any combination of letters, digits and underscore, "%s" given.',
                        $itemType,
                    ),
                );
            }

            $itemTypeName = $itemTypeConfig['name'];
            $parameters = $itemTypeConfig['parameters'];
            unset($itemTypeConfig['name'], $itemTypeConfig['parameters']);

            $configServiceName = sprintf('netgen_content_browser.config.%s', $itemType);

            $container
                ->register($configServiceName, Configuration::class)
                ->addArgument($itemType)
                ->addArgument($itemTypeName)
                ->addArgument($itemTypeConfig)
                ->addArgument($parameters);

            $foundBackend = null;

            foreach ($backendServices as $backend => $tags) {
                foreach ($tags as $tag) {
                    if (($tag['item_type'] ?? '') === $itemType) {
                        $foundBackend = $backend;

                        break 2;
                    }
                }
            }

            if (!is_string($foundBackend)) {
                throw new RuntimeException(
                    sprintf(
                        'No backend registered for "%s" item type. Make sure that "%s" attribute exists in the tag or your backend uses AsBackend attribute.',
                        $itemType,
                        'item_type',
                    ),
                );
            }

            $backends[$itemType] = new Reference($foundBackend);
            $configs[$itemType] = new Reference($configServiceName);

            $container->registerAliasForArgument(
                $configServiceName,
                Configuration::class,
                $itemType . 'Config',
            );
        }

        $backendRegistry->replaceArgument(0, $backends);
        $configRegistry->replaceArgument(0, $configs);

        $container->getParameterBag()->remove('netgen_content_browser.item_types');
    }
}
