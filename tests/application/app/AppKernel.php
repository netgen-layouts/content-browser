<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Kernel;

use Symfony\Bundle\WebServerBundle\WebServerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel implements CompilerPassInterface
{
    public function registerBundles(): iterable
    {
        $bundles = [
            // Symfony

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),

            // Other dependencies

            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Netgen Content Browser

            new \Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle(),
            new \Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle(),
        ];

        // @deprecated Remove class_exists check when support for Symfony 2.8 ends
        if (class_exists(WebServerBundle::class)) {
            $bundles[] = new WebServerBundle();
        }

        return $bundles;
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

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function process(ContainerBuilder $container): void
    {
        if (Kernel::VERSION_ID < 40100) {
            return;
        }

        $container
            ->findDefinition('netgen_content_browser.item_renderer')
            ->setPublic(true);
    }

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }

    protected function prepareContainer(ContainerBuilder $container): void
    {
        parent::prepareContainer($container);

        if (Kernel::VERSION_ID < 30200) {
            // @deprecated Symfony 2.8 does not have kernel.project_dir parameter,
            // so we need to set the parameter to the container manually
            $container->setParameter('kernel.project_dir', $this->getProjectDir());
        }
    }
}
