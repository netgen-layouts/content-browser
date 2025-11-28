<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    public function __construct(
        public private(set) int $locationId,
        public private(set) string $name,
        public private(set) ?int $parentId = null,
    ) {}
}
