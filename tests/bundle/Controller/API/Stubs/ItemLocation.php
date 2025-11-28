<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class ItemLocation implements ItemInterface, LocationInterface
{
    public int $locationId {
        get => $this->value;
    }

    public true $isVisible {
        get => true;
    }

    public true $isSelectable {
        get => true;
    }

    public function __construct(
        public private(set) int $value,
        public private(set) string $name,
        public private(set) ?int $parentId = null,
    ) {}
}
