<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Psr\Container\ContainerInterface;

use function array_key_exists;

final class Container implements ContainerInterface
{
    /**
     * @var array<string, object>
     */
    private array $entries;

    /**
     * @param array<string, object> $entries
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    public function get($id): object
    {
        return $this->entries[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}
