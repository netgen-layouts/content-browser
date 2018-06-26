<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\ContentBrowser\Backend\BackendInterface;

interface BackendRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a backend.
     */
    public function hasBackend(string $itemType): bool;

    /**
     * Returns a backend for provided item type.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If backend does not exist
     */
    public function getBackend(string $itemType): BackendInterface;

    /**
     * Returns all backends.
     *
     * @return \Netgen\ContentBrowser\Backend\BackendInterface[]
     */
    public function getBackends(): array;
}
