<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    public function __construct(
        private(set) int $locationId,
        private(set) string $name,
        private(set) ?int $parentId = null,
    ) {}
}
