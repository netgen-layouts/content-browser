<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\ContentBrowser\Config\ConfigurationInterface;

interface ConfigRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a config with specified item type.
     */
    public function hasConfig(string $itemType): bool;

    /**
     * Returns the config for specified item type.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config does not exist
     */
    public function getConfig(string $itemType): ConfigurationInterface;

    /**
     * Returns all configs.
     *
     * @return \Netgen\ContentBrowser\Config\ConfigurationInterface[]
     */
    public function getConfigs(): array;
}
