<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Traversable;

final class ConfigRegistry implements ConfigRegistryInterface
{
    /**
     * @var \Netgen\ContentBrowser\Config\Configuration[]
     */
    private $configs;

    /**
     * @param \Netgen\ContentBrowser\Config\Configuration[] $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = array_filter(
            $configs,
            function (Configuration $config): bool {
                return true;
            }
        );
    }

    public function hasConfig(string $itemType): bool
    {
        return isset($this->configs[$itemType]);
    }

    public function getConfig(string $itemType): Configuration
    {
        if (!$this->hasConfig($itemType)) {
            throw new InvalidArgumentException(
                sprintf('Configuration for item type "%s" does not exist.', $itemType)
            );
        }

        return $this->configs[$itemType];
    }

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
     *
     * @return mixed
     */
    public function offsetGet($offset)
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
