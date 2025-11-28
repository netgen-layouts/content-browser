<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Event;

use Netgen\ContentBrowser\Config\Configuration;
use Symfony\Contracts\EventDispatcher\Event;

final class ConfigLoadEvent extends Event
{
    /**
     * Returns the item type for which the configuration is being loaded.
     */
    public string $itemType {
        get => $this->config->getItemType();
    }

    public function __construct(
        /**
         * Returns the configuration which is being loaded.
         */
        public private(set) Configuration $config,
    ) {}
}
