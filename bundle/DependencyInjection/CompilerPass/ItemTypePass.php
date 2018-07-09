<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ItemTypePass implements CompilerPassInterface
{
    private const BACKEND_REGISTRY_SERVICE = 'netgen_content_browser.registry.backend';
    private const CONFIG_REGISTRY_SERVICE = 'netgen_content_browser.registry.config';
    private const BACKEND_TAG_NAME = 'netgen_content_browser.backend';

    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->has(self::BACKEND_REGISTRY_SERVICE) ||
            !$container->has(self::CONFIG_REGISTRY_SERVICE)
        ) {
            return;
        }

        $backendRegistry = $container->findDefinition(self::BACKEND_REGISTRY_SERVICE);
        $configRegistry = $container->findDefinition(self::CONFIG_REGISTRY_SERVICE);

        $backendServices = $container->findTaggedServiceIds(self::BACKEND_TAG_NAME);

        $backends = [];
        $configs = [];

        $itemTypesConfig = $container->getParameter('netgen_content_browser.item_types');

        foreach ($itemTypesConfig as $itemType => $itemTypeConfig) {
            if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $itemType) !== 1) {
                throw new RuntimeException(
                    sprintf(
                        'Item type must begin with a letter and be followed by any combination of letters, digits and underscore, "%s" given.',
                        $itemType
                    )
                );
            }

            $itemTypeName = $itemTypeConfig['name'];
            $parameters = $itemTypeConfig['parameters'];
            unset($itemTypeConfig['name'], $itemTypeConfig['parameters']);

            $configServiceName = sprintf('netgen_content_browser.config.%s', $itemType);

            $container
                ->register($configServiceName, Configuration::class)
                ->setPublic(true)
                ->addArgument($itemType)
                ->addArgument($itemTypeName)
                ->addArgument($itemTypeConfig)
                ->addArgument($parameters);

            $foundBackend = null;

            foreach ($backendServices as $backend => $tags) {
                foreach ($tags as $tag) {
                    if (!isset($tag['item_type'])) {
                        throw new RuntimeException(
                            'Backend definition must have an "item_type" attribute in its tag.'
                        );
                    }

                    if ($tag['item_type'] === $itemType) {
                        $foundBackend = $backend;
                        break 2;
                    }
                }
            }

            if (!is_string($foundBackend)) {
                throw new RuntimeException(
                    sprintf('No backend registered for "%s" item type.', $itemType)
                );
            }

            $backends[$itemType] = new Reference($foundBackend);
            $configs[$itemType] = new Reference($configServiceName);
        }

        $backendRegistry->replaceArgument(0, $backends);
        $configRegistry->replaceArgument(0, $configs);

        $container->getParameterBag()->remove('netgen_content_browser.item_types');
    }
}
