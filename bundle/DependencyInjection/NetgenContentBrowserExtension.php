<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
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
            $config['item_types']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yaml');

        $this->doPrepend($container, 'framework/twig.yaml', 'twig');
    }

    /**
     * @param mixed[] $config
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    private function doPrepend(ContainerBuilder $container, string $fileName, string $configName): void
    {
        $configFile = __DIR__ . '/../Resources/config/' . $fileName;
        $config = Yaml::parse((string) file_get_contents($configFile));
        $container->prependExtensionConfig($configName, $config);
        $container->addResource(new FileResource($configFile));
    }
}
