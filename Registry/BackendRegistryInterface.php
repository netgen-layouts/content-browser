<?php

namespace Netgen\Bundle\ContentBrowserBundle\Registry;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;

interface BackendRegistryInterface
{
    /**
     * Adds a backend to registry.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
     */
    public function addBackend(BackendInterface $backend);

    /**
     * Returns if registry has a backend.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasBackend($valueType);

    /**
     * Returns a backend for provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If backend does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    public function getBackend($valueType);

    /**
     * Returns all backends.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface[]
     */
    public function getBackends();
}
