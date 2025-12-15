<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Traversable;

use function array_filter;
use function array_key_exists;
use function count;
use function sprintf;

/**
 * @implements \ArrayAccess<string, \Netgen\ContentBrowser\Config\Configuration>
 * @implements \IteratorAggregate<string, \Netgen\ContentBrowser\Config\Configuration>
 */
final class ConfigRegistry implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param array<string, \Netgen\ContentBrowser\Config\Configuration> $configs
     */
    public function __construct(
        private array $configs,
    ) {
        $this->configs = array_filter(
            $this->configs,
            static fn (Configuration $config): bool => true,
        );
    }

    /**
     * Returns if registry has a config with specified item type.
     */
    public function hasConfig(string $itemType): bool
    {
        return array_key_exists($itemType, $this->configs);
    }

    /**
     * Returns the config for specified item type.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config does not exist
     */
    public function getConfig(string $itemType): Configuration
    {
        if (!$this->hasConfig($itemType)) {
            throw new InvalidArgumentException(
                sprintf('Configuration for item type "%s" does not exist.', $itemType),
            );
        }

        return $this->configs[$itemType];
    }

    /**
     * Returns all configs.
     *
     * @return array<string, \Netgen\ContentBrowser\Config\Configuration>
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->configs);
    }

    public function count(): int
    {
        return count($this->configs);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasConfig($offset);
    }

    public function offsetGet(mixed $offset): Configuration
    {
        return $this->getConfig($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
