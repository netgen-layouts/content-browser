<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function is_string;
use function method_exists;
use function preg_match;
use function sprintf;

final class ItemTypePass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const BACKEND_REGISTRY_SERVICE = 'netgen_content_browser.registry.backend';
    private const CONFIG_REGISTRY_SERVICE = 'netgen_content_browser.registry.config';
    private const BACKEND_TAG_NAME = 'netgen_content_browser.backend';

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

        $backendServices = $container->findTaggedServiceIds(self::BACKEND_TAG_NAME);

        $backends = [];
        $configs = [];

        /** @var array<string, mixed[]> $itemTypesConfig */
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
                $backendClass = $this->getDefinitionClass($container, $backend);

                foreach ($tags as $tag) {
                    if (($tag['item_type'] ?? '') === $itemType) {
                        $foundBackend = $backend;

                        break 2;
                    }
                }

                if (($backendClass::$defaultItemType ?? '') === $itemType) {
                    $foundBackend = $backend;

                    break;
                }
            }

            if (!is_string($foundBackend)) {
                throw new RuntimeException(
                    sprintf(
                        'No backend registered for "%s" item type. Make sure that either "%s" attribute exists in the tag or a "%s" static property exists in the class.',
                        $itemType,
                        'item_type',
                        '$defaultItemType'
                    )
                );
            }

            $backends[$itemType] = new Reference($foundBackend);
            $configs[$itemType] = new Reference($configServiceName);

            // The check is deprecated and serves to support Symfony 3.4 where
            // this method is missing
            if (method_exists($container, 'registerAliasForArgument')) {
                $container->registerAliasForArgument(
                    $configServiceName,
                    Configuration::class,
                    $itemType . 'Config'
                );
            }
        }

        $backendRegistry->replaceArgument(0, $backends);
        $configRegistry->replaceArgument(0, $configs);

        $container->getParameterBag()->remove('netgen_content_browser.item_types');
    }
}
