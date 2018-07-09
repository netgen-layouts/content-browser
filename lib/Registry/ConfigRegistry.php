<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;

final class ConfigRegistry implements ConfigRegistryInterface
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface[]
     */
    private $configs = [];

    /**
     * @param \Netgen\ContentBrowser\Config\ConfigurationInterface[] $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = array_filter(
            $configs,
            function (ConfigurationInterface $config): bool {
                return true;
            }
        );
    }

    public function hasConfig(string $itemType): bool
    {
        return isset($this->configs[$itemType]);
    }

    public function getConfig(string $itemType): ConfigurationInterface
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

    public function getIterator()
    {
        return new ArrayIterator($this->configs);
    }

    public function count()
    {
        return count($this->configs);
    }

    public function offsetExists($offset)
    {
        return $this->hasConfig($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getConfig($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
