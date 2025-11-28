<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure backends.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsBackend
{
    public function __construct(
        public private(set) string $itemType,
    ) {}
}
