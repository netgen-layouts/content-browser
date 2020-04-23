<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Config;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use function sprintf;

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
     * @var array<string, mixed>
     */
    private $config;

    /**
     * @var array<string, mixed>
     */
    private $parameters;

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $parameters
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getColumns(): array
    {
        return $this->config['columns'] ?? [];
    }

    /**
     * @return string[]
     */
    public function getDefaultColumns(): array
    {
        return $this->config['default_columns'] ?? [];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters = $parameters + $this->parameters;
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
