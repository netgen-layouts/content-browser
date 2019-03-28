<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Traversable;

final class BackendRegistry implements BackendRegistryInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface[]
     */
    private $backends;

    /**
     * @param \Netgen\ContentBrowser\Backend\BackendInterface[] $backends
     */
    public function __construct(array $backends)
    {
        $this->backends = array_filter(
            $backends,
            function (BackendInterface $backend): bool {
                return true;
            }
        );
    }

    public function hasBackend(string $itemType): bool
    {
        return isset($this->backends[$itemType]);
    }

    public function getBackend(string $itemType): BackendInterface
    {
        if (!$this->hasBackend($itemType)) {
            throw new InvalidArgumentException(
                sprintf('Backend for "%s" item type does not exist.', $itemType)
            );
        }

        return $this->backends[$itemType];
    }

    public function getBackends(): array
    {
        return $this->backends;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->backends);
    }

    public function count(): int
    {
        return count($this->backends);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBackend($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getBackend($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
