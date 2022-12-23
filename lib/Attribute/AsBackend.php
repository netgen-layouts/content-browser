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
    public string $itemType;

    public function __construct(string $itemType)
    {
        $this->itemType = $itemType;
    }
}
