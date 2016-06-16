<?php

namespace Netgen\Bundle\ContentBrowserBundle\Registry;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;

class BackendRegistry implements BackendRegistryInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface[]
     */
    protected $backends = array();

    /**
     * Adds a backend to registry.
     *
     * @param string $valueType
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
     */
    public function addBackend($valueType, BackendInterface $backend)
    {
        $this->backends[$valueType] = $backend;
    }

    /**
     * Returns if registry has a backend.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasBackend($valueType)
    {
        return isset($this->backends[$valueType]);
    }

    /**
     * Returns a backend for provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If backend does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    public function getBackend($valueType)
    {
        if (!$this->hasBackend($valueType)) {
            throw new InvalidArgumentException(
                sprintf('Backend for "%s" value type does not exist.', $valueType)
            );
        }

        return $this->backends[$valueType];
    }

    /**
     * Returns all backends.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface[]
     */
    public function getBackends()
    {
        return $this->backends;
    }
}
