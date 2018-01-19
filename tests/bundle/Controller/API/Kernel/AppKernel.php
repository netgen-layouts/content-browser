<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Kernel;

use Netgen\ContentBrowser\Tests\MockerContainer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            // Symfony

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Netgen Content Browser

            new \Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle(),
        );
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/ngcb/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/ngcb/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    protected function getContainerBaseClass()
    {
        return '\\' . MockerContainer::class;
    }
}
