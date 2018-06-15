<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Config;

interface ConfigurationInterface
{
    /**
     * Returns the item type.
     */
    public function getItemType(): string;

    /**
     * Returns the minimum number of items allowed to be selected.
     */
    public function getMinSelected(): int;

    /**
     * Returns the maximum number of items allowed to be selected.
     */
    public function getMaxSelected(): int;

    /**
     * Returns if the tree is activated in the config.
     */
    public function hasTree(): bool;

    /**
     * Returns if the search is activated in the config.
     */
    public function hasSearch(): bool;

    /**
     * Returns if the preview is activated in the config.
     */
    public function hasPreview(): bool;

    /**
     * Returns the template used to render the item or null if the preview is disabled.
     */
    public function getTemplate(): ?string;

    /**
     * Returns the list of columns.
     */
    public function getColumns(): array;

    /**
     * Returns the list of default columns.
     */
    public function getDefaultColumns(): array;

    /**
     * Sets the parameter with specified name to specified value.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void;

    /**
     * Adds the provided parameters to the config.
     *
     * Provided parameters will override any existing parameters.
     */
    public function addParameters(array $parameters): void;

    /**
     * Returns if config has the specified parameter.
     */
    public function hasParameter(string $name): bool;

    /**
     * Returns the parameter with specified name.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException if parameter does not exist
     *
     * @return mixed
     */
    public function getParameter(string $name);

    /**
     * Returns all parameters.
     */
    public function getParameters(): array;
}
