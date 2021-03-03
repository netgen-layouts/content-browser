<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class LocationItem implements ItemInterface, LocationInterface
{
    private int $value;

    private int $locationId;

    private ?int $parentId;

    public function __construct(int $value, int $locationId, ?int $parentId = null)
    {
        $this->value = $value;
        $this->locationId = $locationId;
        $this->parentId = $parentId;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return 'This is a name (' . $this->value . ')';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
