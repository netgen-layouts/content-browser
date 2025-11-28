<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class LocationItem implements ItemInterface, LocationInterface
{
    public string $name {
        get => 'This is a name (' . $this->value . ')';
    }

    public true $isVisible {
        get => true;
    }

    public true $isSelectable {
        get => true;
    }

    public function __construct(
        public private(set) int $value,
        public private(set) int $locationId,
        public private(set) ?int $parentId = null,
    ) {}
}
