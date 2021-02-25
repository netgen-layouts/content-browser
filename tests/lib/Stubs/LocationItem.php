<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class LocationItem implements ItemInterface, LocationInterface
{
    private ?int $value;

    private ?int $locationId;

    private ?int $parentId;

    public function __construct(?int $value = null, ?int $locationId = null, ?int $parentId = null)
    {
        $this->value = $value;
        $this->locationId = $locationId;
        $this->parentId = $parentId;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function getName(): string
    {
        $name = 'This is a name';

        if ($this->value !== null) {
            $name .= ' (' . $this->value . ')';
        }

        return $name;
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
