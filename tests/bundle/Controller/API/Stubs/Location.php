<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    public function __construct(
        private int $locationId,
        private string $name,
        private ?int $parentId = null,
    ) {}

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
