<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Config;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;

final class Configuration
{
    /**
     * @var string
     */
    private $itemType;

    /**
     * @var string
     */
    private $itemName;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        string $itemType,
        string $itemName,
        array $config,
        array $parameters = []
    ) {
        $this->itemType = $itemType;
        $this->itemName = $itemName;
        $this->config = $config;
        $this->parameters = $parameters;
    }

    public function getItemType(): string
    {
        return $this->itemType;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getMinSelected(): int
    {
        return $this->config['min_selected'] ?? 1;
    }

    public function getMaxSelected(): int
    {
        return $this->config['max_selected'] ?? 0;
    }

    public function hasTree(): bool
    {
        return $this->config['tree']['enabled'] ?? false;
    }

    public function hasSearch(): bool
    {
        return $this->config['search']['enabled'] ?? false;
    }

    public function hasPreview(): bool
    {
        return $this->config['preview']['enabled'] ?? false;
    }

    public function getTemplate(): ?string
    {
        return $this->config['preview']['template'] ?? null;
    }

    public function getColumns(): array
    {
        return $this->config['columns'] ?? [];
    }

    public function getDefaultColumns(): array
    {
        return $this->config['default_columns'] ?? [];
    }

    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function addParameters(array $parameters): void
    {
        $this->parameters = $parameters + $this->parameters;
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    public function getParameter(string $name)
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

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
