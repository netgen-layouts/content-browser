<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel implements CompilerPassInterface
{
    public function registerBundles(): iterable
    {
        return [
            // Symfony

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\WebServerBundle\WebServerBundle(),

            // Other dependencies

            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Netgen Content Browser

            new \Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle(),
            new \Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle(),
        ];
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

        $container
            ->findDefinition('netgen_content_browser.registry.backend')
            ->setPublic(true);
    }

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }
}
