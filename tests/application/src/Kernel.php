<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function dirname;
use function sys_get_temp_dir;

final class Kernel extends BaseKernel
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
}
