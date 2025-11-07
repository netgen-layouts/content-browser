<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure column value providers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsColumnValueProvider
{
    public function __construct(
        public string $identifier,
    ) {}
}
