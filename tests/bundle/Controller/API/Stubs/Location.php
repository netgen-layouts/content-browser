<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    private int $locationId;

    private string $name;

    private ?int $parentId;

    public function __construct(int $locationId, string $name, ?int $parentId = null)
    {
        $this->locationId = $locationId;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
