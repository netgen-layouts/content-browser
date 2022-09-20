<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function dirname;
use function sys_get_temp_dir;

final class LegacyKernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/ngcb/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/ngcb/logs';
    }

    public function process(ContainerBuilder $container): void
    {
        $container
            ->findDefinition('netgen_content_browser.item_renderer')
            ->setPublic(true);

        $container
            ->findDefinition('netgen_content_browser.registry.backend')
            ->setPublic(true);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/legacy/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    /**
     * @param \Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator|\Symfony\Component\Routing\RouteCollectionBuilder $routes
     */
    protected function configureRoutes($routes): void
    {
        if ($routes instanceof RoutingConfigurator) {
            $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
            $routes->import('../config/{routes}/*.yaml');
            $routes->import('../config/{routes}.yaml');

            return;
        }

        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }
}
