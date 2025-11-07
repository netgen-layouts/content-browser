<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Event;

use Netgen\ContentBrowser\Config\Configuration;
use Symfony\Contracts\EventDispatcher\Event;

final class ConfigLoadEvent extends Event
{
    public function __construct(
        private Configuration $config,
    ) {}

    /**
     * Returns the configuration which is being loaded.
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Returns the item type for which the configuration is being loaded.
     */
    public function getItemType(): string
    {
        return $this->config->getItemType();
    }
}
