<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Netgen\ContentBrowser\Attribute;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

use function file_get_contents;
use function sprintf;

final class NetgenContentBrowserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            sprintf('%s.%s', $this->getAlias(), 'item_types'),
            $config['item_types'],
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('services.yaml');
        $loader->load('autowiring.yaml');

        $this->registerAutoConfiguration($container);
        $this->registerAttributeAutoConfiguration($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('default_settings.yaml');

        $prependConfigs = [
            'framework/twig.yaml' => 'twig',
        ];

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * @param mixed[] $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration($this);
    }

    private function registerAutoConfiguration(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(BackendInterface::class)
            ->addTag('netgen_content_browser.backend');

        $container
            ->registerForAutoconfiguration(ColumnValueProviderInterface::class)
            ->addTag('netgen_content_browser.column_value_provider');
    }

    private function registerAttributeAutoConfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            Attribute\AsBackend::class,
            static function (ChildDefinition $definition, Attribute\AsBackend $attribute): void {
                $definition->addTag('netgen_content_browser.backend', ['item_type' => $attribute->itemType]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsColumnValueProvider::class,
            static function (ChildDefinition $definition, Attribute\AsColumnValueProvider $attribute): void {
                $definition->addTag('netgen_content_browser.column_value_provider', ['identifier' => $attribute->identifier]);
            },
        );
    }
}
