<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Event;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Utils\BackwardsCompatibility\Event;

final class ConfigLoadEvent extends Event
{
    private Configuration $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

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
