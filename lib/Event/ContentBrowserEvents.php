<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Event;

final class ContentBrowserEvents
{
    /**
     * This event will be dispatched when the configuration for current backend is loaded.
     */
    public const CONFIG_LOAD = 'ngcb.config_load';
}
