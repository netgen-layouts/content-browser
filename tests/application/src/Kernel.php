<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function dirname;
use function sys_get_temp_dir;

final class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

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

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }
}
