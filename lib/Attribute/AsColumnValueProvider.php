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
    public string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
}
