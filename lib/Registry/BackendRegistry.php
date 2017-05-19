<?php

namespace Netgen\ContentBrowser\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;

class BackendRegistry implements BackendRegistryInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface[]
     */
    protected $backends = array();

    /**
     * Adds a backend to registry.
     *
     * @param string $itemType
     * @param \Netgen\ContentBrowser\Backend\BackendInterface $backend
     */
    public function addBackend($itemType, BackendInterface $backend)
    {
        $this->backends[$itemType] = $backend;
    }

    /**
     * Returns if registry has a backend.
     *
     * @param string $itemType
     *
     * @return bool
     */
    public function hasBackend($itemType)
    {
        return isset($this->backends[$itemType]);
    }

    /**
     * Returns a backend for provided item type.
     *
     * @param string $itemType
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If backend does not exist
     *
     * @return \Netgen\ContentBrowser\Backend\BackendInterface
     */
    public function getBackend($itemType)
    {
        if (!$this->hasBackend($itemType)) {
            throw new InvalidArgumentException(
                sprintf('Backend for "%s" item type does not exist.', $itemType)
            );
        }

        return $this->backends[$itemType];
    }

    /**
     * Returns all backends.
     *
     * @return \Netgen\ContentBrowser\Backend\BackendInterface[]
     */
    public function getBackends()
    {
        return $this->backends;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->backends);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->backends);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasBackend($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getBackend($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
