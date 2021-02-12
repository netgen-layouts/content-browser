<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Traversable;
use function array_filter;
use function count;
use function sprintf;

/**
 * @implements \IteratorAggregate<string, \Netgen\ContentBrowser\Backend\BackendInterface>
 * @implements \ArrayAccess<string, \Netgen\ContentBrowser\Backend\BackendInterface>
 */
final class BackendRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\ContentBrowser\Backend\BackendInterface>
     */
    private array $backends;

    /**
     * @param array<string, \Netgen\ContentBrowser\Backend\BackendInterface> $backends
     */
    public function __construct(array $backends)
    {
        $this->backends = array_filter(
            $backends,
            static fn (BackendInterface $backend): bool => true
        );
    }

    /**
     * Returns if registry has a backend.
     */
    public function hasBackend(string $itemType): bool
    {
        return isset($this->backends[$itemType]);
    }

    /**
     * Returns a backend for provided item type.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If backend does not exist
     */
    public function getBackend(string $itemType): BackendInterface
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
     * @return array<string, \Netgen\ContentBrowser\Backend\BackendInterface>
     */
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
     */
    public function offsetGet($offset): BackendInterface
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
