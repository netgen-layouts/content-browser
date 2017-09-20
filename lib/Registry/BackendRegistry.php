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
    private $backends = array();

    public function addBackend($itemType, BackendInterface $backend)
    {
        $this->backends[$itemType] = $backend;
    }

    public function hasBackend($itemType)
    {
        return isset($this->backends[$itemType]);
    }

    public function getBackend($itemType)
    {
        if (!$this->hasBackend($itemType)) {
            throw new InvalidArgumentException(
                sprintf('Backend for "%s" item type does not exist.', $itemType)
            );
        }

        return $this->backends[$itemType];
    }

    public function getBackends()
    {
        return $this->backends;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->backends);
    }

    public function count()
    {
        return count($this->backends);
    }

    public function offsetExists($offset)
    {
        return $this->hasBackend($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getBackend($offset);
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
