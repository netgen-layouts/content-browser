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
use function array_key_exists;
use function count;
use function sprintf;

/**
 * @implements \ArrayAccess<string, \Netgen\ContentBrowser\Backend\BackendInterface>
 * @implements \IteratorAggregate<string, \Netgen\ContentBrowser\Backend\BackendInterface>
 */
final class BackendRegistry implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param array<string, \Netgen\ContentBrowser\Backend\BackendInterface> $backends
     */
    public function __construct(
        private array $backends,
    ) {
        $this->backends = array_filter(
            $this->backends,
            static fn (BackendInterface $backend): bool => true,
        );
    }

    /**
     * Returns if registry has a backend.
     */
    public function hasBackend(string $itemType): bool
    {
        return array_key_exists($itemType, $this->backends);
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
                sprintf('Backend for "%s" item type does not exist.', $itemType),
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

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasBackend($offset);
    }

    public function offsetGet(mixed $offset): BackendInterface
    {
        return $this->getBackend($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
