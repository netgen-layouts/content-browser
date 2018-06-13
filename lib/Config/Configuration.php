<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Config;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $itemType;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @param string $itemType
     * @param array $config
     * @param array $parameters
     */
    public function __construct($itemType, array $config = [], array $parameters = [])
    {
        $this->itemType = $itemType;
        $this->config = $config;
        $this->parameters = $parameters;
    }

    public function getItemType()
    {
        return $this->itemType;
    }

    public function getMinSelected()
    {
        return $this->config['min_selected'] ?? 1;
    }

    public function getMaxSelected()
    {
        return $this->config['max_selected'] ?? 0;
    }

    public function hasTree()
    {
        return $this->config['tree']['enabled'] ?? false;
    }

    public function hasSearch()
    {
        return $this->config['search']['enabled'] ?? false;
    }

    public function hasPreview()
    {
        return $this->config['preview']['enabled'] ?? false;
    }

    public function getTemplate()
    {
        return $this->config['preview']['template'] ?? null;
    }

    public function getColumns()
    {
        return $this->config['columns'] ?? [];
    }

    public function getDefaultColumns()
    {
        return $this->config['default_columns'] ?? [];
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function addParameters(array $parameters)
    {
        $this->parameters = $parameters + $this->parameters;
    }

    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    public function getParameter($name)
    {
        if (!$this->hasParameter($name)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "%s" does not exist in configuration.',
                    $name
                )
            );
        }

        return $this->parameters[$name];
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
