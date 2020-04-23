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
use function count;
use function sprintf;

/**
 * @implements \IteratorAggregate<string, \Netgen\ContentBrowser\Config\Configuration>
 * @implements \ArrayAccess<string, \Netgen\ContentBrowser\Config\Configuration>
 */
final class ConfigRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\ContentBrowser\Config\Configuration>
     */
    private $configs;

    /**
     * @param array<string, \Netgen\ContentBrowser\Config\Configuration> $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = array_filter(
            $configs,
            static function (Configuration $config): bool {
                return true;
            }
        );
    }

    /**
     * Returns if registry has a config with specified item type.
     */
    public function hasConfig(string $itemType): bool
    {
        return isset($this->configs[$itemType]);
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
                sprintf('Configuration for item type "%s" does not exist.', $itemType)
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

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasConfig($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): Configuration
    {
        return $this->getConfig($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
