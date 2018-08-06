<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Event;

use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Symfony\Component\EventDispatcher\Event;

final class ConfigLoadEvent extends Event
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $config;

    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the configuration which is being loaded.
     */
    public function getConfig(): ConfigurationInterface
    {
        return $this->config;
    }
}
